<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\NotificationSubscription;

class FakeNotificationSubscription extends FakeEntity {
    public static function defaultNotificationSubscription($fresh = false) {
        return self::getFake(
            'default_notification_subscription',
            $fresh,
            function () {
                $notification_subscription = new NotificationSubscription();
                $notification_subscription->setId(1);
                $notification_subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $notification_subscription->setUser(FakeUsers::defaultUser());
                $notification_subscription->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
                $notification_subscription->setNotificationTypeArgs(json_encode([]));
                $notification_subscription->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                return $notification_subscription;
            }
        );
    }
}
