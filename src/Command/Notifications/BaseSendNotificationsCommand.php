<?php

namespace Olz\Command\Notifications;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;

abstract class BaseSendNotificationsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    abstract public function getNotificationSubscriptionType(): string;

    abstract public function autogenerateSubscriptions(): void;

    /** @param array<string, mixed> $args */
    abstract public function getNotification(array $args): ?Notification;

    /** @return array<string> */
    protected function getNonReminderNotificationTypes(): array {
        return array_values(array_filter(
            NotificationSubscription::ALL_NOTIFICATION_TYPES,
            function ($notification_type): bool {
                return
                    $notification_type !== NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER
                    && $notification_type !== NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER
                    && $notification_type !== NotificationSubscription::TYPE_ROLE_REMINDER;
            }
        ));
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->autogenerateSubscriptions();

        $subscriptions_by_args = $this->getNotificationSubscriptions();
        $this->sendNotifications($subscriptions_by_args);

        return Command::SUCCESS;
    }

    /** @return array<string, array<NotificationSubscription>> */
    private function getNotificationSubscriptions(): array {
        $type = $this->getNotificationSubscriptionType();
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findBy(['notification_type' => $type]);

        $subscriptions_by_args = [];
        foreach ($subscriptions as $subscription) {
            $notification_args = $subscription->getNotificationTypeArgs() ?? '';
            $subscriptions_of_args = $subscriptions_by_args[$notification_args] ?? [];
            $subscriptions_of_args[] = $subscription;
            $subscriptions_by_args[$notification_args] = $subscriptions_of_args;
        }
        return $subscriptions_by_args;
    }

    /** @param array<string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendNotifications(array $subscriptions_by_args): void {
        $this->log()->info("Sending '{$this->getNotificationSubscriptionType()}' notifications...");
        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $this->getNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    private function sendNotificationToSubscription(Notification $notification, NotificationSubscription $subscription): void {
        $user = $subscription->getUser();
        $title = $notification->title;
        $text = $notification->getTextForUser($user);
        $config = $notification->config;
        $subscription_id = $subscription->getId();
        $delivery_type = $subscription->getDeliveryType();
        $user_id = $user->getId();
        $this->log()->info("Sending notification {$title} over {$delivery_type} to user ({$user_id})...");
        switch ($delivery_type) {
            case NotificationSubscription::DELIVERY_EMAIL:
                try {
                    $email = (new Email())->subject("[OLZ] {$title}");
                    $email = $this->emailUtils()->buildOlzEmail($email, $user, $text, $config);
                    $this->emailUtils()->send($email);
                    $this->log()->info("Email sent to user ({$user_id}): {$title}");
                } catch (\Throwable $th) {
                    $th_class = get_class($th);
                    $message = $th->getMessage();
                    $this->log()->critical("Error sending email to user ({$user_id}): [{$th_class}] {$message}", []);
                }
                break;
            case NotificationSubscription::DELIVERY_TELEGRAM:
                $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
                $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
                if (!$telegram_link) {
                    $this->log()->notice("User ({$user_id}) has no telegram link, but a subscription ({$subscription_id})");
                    return;
                }
                $user_chat_id = $telegram_link->getTelegramChatId();
                if (!$user_chat_id) {
                    $this->log()->critical("User ({$user_id}) has a telegram link without chat ID, but a subscription ({$subscription_id})");
                    return;
                }
                $html_title = $this->telegramUtils()->renderMarkdown($title);
                $html_text = $this->telegramUtils()->renderMarkdown($text);
                try {
                    $this->telegramUtils()->callTelegramApi('sendMessage', [
                        'chat_id' => $user_chat_id,
                        'parse_mode' => 'HTML',
                        'text' => "<b>{$html_title}</b>\n\n{$html_text}",
                        'disable_web_page_preview' => true,
                    ]);
                    $this->log()->info("Telegram sent to user ({$user_id}): {$title}");
                } catch (\Throwable $th) {
                    $th_class = get_class($th);
                    $message = $th->getMessage();
                    $this->log()->notice("Error sending telegram to user ({$user_id}): [{$th_class}] {$message}", []);
                }
                break;
            default:
                $this->log()->critical("Unknown delivery type '{$delivery_type}'");
                break;
        }
    }
}
