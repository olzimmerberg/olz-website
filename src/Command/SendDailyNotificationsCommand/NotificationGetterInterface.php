<?php

namespace Olz\Command\SendDailyNotificationsCommand;

interface NotificationGetterInterface {
    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification;

    /** @param array<string, mixed> $all_utils */
    public function setAllUtils(array $all_utils): void;
}
