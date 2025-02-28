<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Command\SendDailyNotificationsCommand\EmailConfigurationReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter;
use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Command\SendDailyNotificationsCommand\NotificationGetterInterface;
use Olz\Command\SendDailyNotificationsCommand\RoleReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'olz:send-daily-notifications')]
class SendDailyNotificationsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    /** @var array<string, NotificationGetterInterface> */
    protected array $notification_getter_by_type;

    public function __construct() {
        parent::__construct();
        $this->notification_getter_by_type = [
            NotificationSubscription::TYPE_DAILY_SUMMARY => new DailySummaryGetter(),
            NotificationSubscription::TYPE_DEADLINE_WARNING => new DeadlineWarningGetter(),
            NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER => new EmailConfigurationReminderGetter(),
            NotificationSubscription::TYPE_MONTHLY_PREVIEW => new MonthlyPreviewGetter(),
            NotificationSubscription::TYPE_ROLE_REMINDER => new RoleReminderGetter(),
            NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER => new TelegramConfigurationReminderGetter(),
            NotificationSubscription::TYPE_WEEKLY_PREVIEW => new WeeklyPreviewGetter(),
            NotificationSubscription::TYPE_WEEKLY_SUMMARY => new WeeklySummaryGetter(),
        ];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->log()->info("Autogenerating notification subscriptions...");
        foreach ($this->notification_getter_by_type as $type => $getter) {
            $getter->autogenerateSubscriptions();
        }

        $subscriptions_by_type_and_args = $this->getNotificationSubscriptions();
        foreach ($subscriptions_by_type_and_args as $type => $subscriptions_by_args) {
            $this->sendNotifications($type, $subscriptions_by_args);
        }

        return Command::SUCCESS;
    }

    /** @return array<string, array<?string, array<NotificationSubscription>>> */
    private function getNotificationSubscriptions(): array {
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findAll();

        $subscriptions_by_type_and_args = [];
        foreach ($subscriptions as $subscription) {
            $notification_type = $subscription->getNotificationType();
            $notification_args = $subscription->getNotificationTypeArgs();
            $subscriptions_by_args_of_type = $subscriptions_by_type_and_args[$notification_type] ?? [];
            $subscriptions_of_type_and_args = $subscriptions_by_args_of_type[$notification_args] ?? [];
            $subscriptions_of_type_and_args[] = $subscription;
            $subscriptions_by_args_of_type[$notification_args] = $subscriptions_of_type_and_args;
            $subscriptions_by_type_and_args[$notification_type] = $subscriptions_by_args_of_type;
        }
        return $subscriptions_by_type_and_args;
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendNotifications(string $type, array $subscriptions_by_args): void {
        $this->log()->info("Sending '{$type}' notifications...");

        $notification_getter = $this->notification_getter_by_type[$type] ?? null;
        if (!$notification_getter) {
            $this->log()->critical("Unknown notification type '{$type}'");
            return;
        }
        $notification_getter->setAllUtils($this->getAllUtils()); // TODO: Necessary?

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $notification_getter->getNotification($args);
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
