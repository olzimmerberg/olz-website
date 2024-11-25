<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Olz\Entity\NotificationSubscription;

trait NotificationGetterTrait {
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
}
