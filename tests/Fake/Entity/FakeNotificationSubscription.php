<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\NotificationSubscription;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeNotificationSubscription extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            'minimal',
            $fresh,
            function () {
                $entity = new NotificationSubscription();
                $entity->setId(12);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
                $entity->setNotificationTypeArgs('{}');
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            'empty',
            $fresh,
            function () {
                $entity = new NotificationSubscription();
                $entity->setId(123);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs('{}');
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            'maximal',
            $fresh,
            function () {
                $entity = new NotificationSubscription();
                $entity->setId(1234);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
                $entity->setNotificationTypeArgs('{}');
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function defaultNotificationSubscription($fresh = false) {
        return self::getFake(
            'default_notification_subscription',
            $fresh,
            function () {
                $notification_subscription = new NotificationSubscription();
                $notification_subscription->setId(1);
                $notification_subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $notification_subscription->setUser(FakeUser::defaultUser());
                $notification_subscription->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
                $notification_subscription->setNotificationTypeArgs(json_encode([]));
                $notification_subscription->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                return $notification_subscription;
            }
        );
    }
}
