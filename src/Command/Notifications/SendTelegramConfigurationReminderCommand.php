<?php

namespace Olz\Command\Notifications;

use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'olz:send-telegram-config-reminder')]
class SendTelegramConfigurationReminderCommand extends BaseSendNotificationsCommand {
    use WithUtilsTrait;

    public const DAY_OF_MONTH = 22;

    public function getNotificationSubscriptionType(): string {
        return NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER;
    }

    public function autogenerateSubscriptions(): void {
        $this->log()->info("Generating telegram configuration reminder subscriptions...");
        $telegram_notifications_state = $this->getTelegramConfigReminderState();

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        foreach ($telegram_notifications_state as $user_id => $state) {
            $reminder_id = $state['reminder_id'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            $user = $user_repo->findOneBy(['id' => $user_id]);
            if (!$user) {
                $this->log()->warning("No user (ID:{$user_id}) for telegram notification");
            }
            if ($needs_reminder && !$reminder_id && $user) {
                $this->log()->info("Generating telegram configuration reminder subscription for '{$user}'...");
                $subscription = new NotificationSubscription();
                $subscription->setUser($user);
                $subscription->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $subscription->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
                $subscription->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '{}');
                $subscription->setCreatedAt($now_datetime);
                $this->entityManager()->persist($subscription);
            }
            if ($reminder_id && !$needs_reminder) {
                $this->log()->info("Removing telegram configuration reminder subscription ({$reminder_id}) for '{$user}'...");
                $subscription = $notification_subscription_repo->findOneBy(['id' => $reminder_id]);
                if ($subscription) {
                    $this->entityManager()->remove($subscription);
                }
            }
        }
        $this->entityManager()->flush();
    }

    /** @return array<int, array{reminder_id?: int, needs_reminder?: bool}> */
    protected function getTelegramConfigReminderState(): array {
        $telegram_notifications_state = [];

        // Find users with existing telegram config reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $telegram_notification_subscriptions = $notification_subscription_repo->findBy([
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]);
        foreach ($telegram_notification_subscriptions as $subscription) {
            $user_id = $subscription->getUser()->getId() ?: 0;
            $user_state = $telegram_notifications_state[$user_id] ?? [];
            $subscription_id = $subscription->getId();
            $this->generalUtils()->checkNotNull($subscription_id, "No subscription ID");
            $user_state['reminder_id'] = $subscription_id;
            $telegram_notifications_state[$user_id] = $user_state;
        }

        // Find users who should have telegram config reminder notification subscriptions.
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->getActivatedTelegramLinks();
        $non_config_reminder_notification_types = $this->getNonReminderNotificationTypes();
        foreach ($telegram_links as $telegram_link) {
            $user = $telegram_link->getUser();
            if (!$user) {
                continue;
            }
            $subscription = $notification_subscription_repo->findOneBy([
                'user' => $user,
                'delivery_type' => NotificationSubscription::DELIVERY_TELEGRAM,
                'notification_type' => $non_config_reminder_notification_types,
            ]);
            if (!$subscription) {
                $user_id = $user->getId() ?: 0;
                $user_state = $telegram_notifications_state[$user_id] ?? [];
                $user_state['needs_reminder'] = true;
                $telegram_notifications_state[$user_id] = $user_state;
            }
        }

        return $telegram_notifications_state;
    }

    // ---

    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification {
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $day_of_month = intval($this->dateUtils()->getCurrentDateInFormat('j'));
        if ($day_of_month !== self::DAY_OF_MONTH) {
            return null;
        }

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $newsletter_url = "{$base_href}{$code_href}apps/newsletter";

        $title = "Keine Push-Nachrichten abonniert";
        $text = <<<ZZZZZZZZZZ
            Hallo %%userFirstName%%,

            Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.


            **Du möchtest eigentlich Push-Nachrichten erhalten?**

            In diesem Fall musst du dich auf der Website *einloggen*, und im ["Newsletter"-App]({$newsletter_url}) (ist auch unter "Service" zu finden) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


            **Du möchtest gar keine Push-Nachrichten erhalten?**

            Dann lösche einfach diesen Chat.


            ZZZZZZZZZZ;

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]);
    }
}
