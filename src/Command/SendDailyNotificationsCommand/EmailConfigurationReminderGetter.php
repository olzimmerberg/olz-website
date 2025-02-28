<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Olz\Entity\NotificationSubscription;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;

class EmailConfigurationReminderGetter implements NotificationGetterInterface {
    use NotificationGetterTrait;
    use WithUtilsTrait;

    public const DAY_OF_MONTH = 22;

    public function autogenerateSubscriptions(): void {
        $email_notifications_state = $this->getEmailConfigReminderState();

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        foreach ($email_notifications_state as $user_id => $state) {
            $reminder_id = $state['reminder_id'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            $user = $user_repo->findOneBy(['id' => $user_id]);
            if (!$user) {
                $this->log()->warning("No user (ID:{$user_id}) for telegram notification");
            }
            if ($needs_reminder && !$reminder_id && $user) {
                $this->log()->info("Generating email configuration reminder subscription for '{$user}'...");
                $subscription = new NotificationSubscription();
                $subscription->setUser($user);
                $subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $subscription->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
                $subscription->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '{}');
                $subscription->setCreatedAt($now_datetime);
                $this->entityManager()->persist($subscription);
            }
            if ($reminder_id && !$needs_reminder) {
                $this->log()->info("Removing email configuration reminder subscription ({$reminder_id}) for '{$user}'...");
                $subscription = $notification_subscription_repo->findOneBy(['id' => $reminder_id]);
                if ($subscription) {
                    $this->entityManager()->remove($subscription);
                }
            }
        }
        $this->entityManager()->flush();
    }

    /** @return array<int, array{reminder_id?: int, needs_reminder?: bool}> */
    protected function getEmailConfigReminderState(): array {
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $minus_one_month = \DateInterval::createFromDateString("-1 months");
        $one_month_ago = $now_datetime->add($minus_one_month);
        $email_notifications_state = [];

        // Find users with existing email config reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $email_notification_subscriptions = $notification_subscription_repo->findBy([
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]);
        foreach ($email_notification_subscriptions as $subscription) {
            $user_id = $subscription->getUser()->getId() ?: 0;
            $user_state = $email_notifications_state[$user_id] ?? [];
            $subscription_id = $subscription->getId();
            $this->generalUtils()->checkNotNull($subscription_id, "No subscription ID");
            $user_state['reminder_id'] = $subscription_id;
            $email_notifications_state[$user_id] = $user_state;
        }

        // Find users who should have email config reminder notification subscriptions.
        $user_repo = $this->entityManager()->getRepository(User::class);
        $users_with_email = $user_repo->getUsersWithLogin();
        $non_config_reminder_notification_types = $this->getNonReminderNotificationTypes();
        foreach ($users_with_email as $user_with_email) {
            $joined_recently = ($user_with_email->getCreatedAt()->getTimestamp() > $one_month_ago->getTimestamp());
            $subscription = $notification_subscription_repo->findOneBy([
                'user' => $user_with_email,
                'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
                'notification_type' => $non_config_reminder_notification_types,
            ]);
            if (!$subscription && $joined_recently) {
                $user_id = $user_with_email->getId() ?: 0;
                $user_state = $email_notifications_state[$user_id] ?? [];
                $user_state['needs_reminder'] = true;
                $email_notifications_state[$user_id] = $user_state;
            }
        }

        return $email_notifications_state;
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

        $title = "Kein Newsletter abonniert";
        $text = <<<ZZZZZZZZZZ
            Hallo %%userFirstName%%,

            Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


            **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**

            In diesem Fall musst du dich auf der Website [*einloggen*]({$newsletter_url}#login-dialog), und im ["Newsletter"-App]({$newsletter_url}) (ist auch unter "Service" zu finden) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

            Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `%%userEmail%%` registriert.


            **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

            Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


            ZZZZZZZZZZ;

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]);
    }
}
