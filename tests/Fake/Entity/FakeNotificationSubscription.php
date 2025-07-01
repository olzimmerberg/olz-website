<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\NotificationSubscription;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<NotificationSubscription>
 */
class FakeNotificationSubscription extends FakeEntity {
    public static function minimal(bool $fresh = false): NotificationSubscription {
        return self::getFake(
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

    public static function empty(bool $fresh = false): NotificationSubscription {
        return self::getFake(
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

    public static function maximal(bool $fresh = false): NotificationSubscription {
        return self::getFake(
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

    public static function defaultNotificationSubscription(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $notification_subscription = new NotificationSubscription();
                $notification_subscription->setId(1);
                $notification_subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $notification_subscription->setUser(FakeUser::defaultUser());
                $notification_subscription->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
                $notification_subscription->setNotificationTypeArgs(json_encode([]) ?: '');
                $notification_subscription->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                return $notification_subscription;
            }
        );
    }

    public static function subscription1(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(1);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
                $entity->setNotificationTypeArgs(json_encode([]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription2(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(2);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
                $entity->setNotificationTypeArgs(json_encode(['no_notification' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription3(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(3);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
                $entity->setNotificationTypeArgs(json_encode([]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription4(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(4);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
                $entity->setNotificationTypeArgs(json_encode(['no_notification' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription5(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(5);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
                $entity->setNotificationTypeArgs(json_encode(['days' => 7]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription6(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(6);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
                $entity->setNotificationTypeArgs(json_encode(['days' => 3]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription7(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(7);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::vorstandUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
                $entity->setNotificationTypeArgs(json_encode(['days' => 3]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription8(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(8);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
                $entity->setNotificationTypeArgs(json_encode(['days' => 3]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription9(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(9);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
                $entity->setNotificationTypeArgs(json_encode(['no_notification' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription10(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(10);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription11(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(11);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['no_notification' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription12(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(12);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription13(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(13);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['no_notification' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription14(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(14);
                $entity->setDeliveryType('invalid-delivery');
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription15(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(15);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType('invalid-type');
                $entity->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription16(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(16);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['provoke_error' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription17(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(17);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::provokeErrorUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription18(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(18);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription19(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(19);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription20(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(20);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription21(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(21);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription22(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(22);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::noTelegramLinkUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
                $entity->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]) ?: '');
                return $entity;
            }
        );
    }

    public static function subscription23(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(23);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_ROLE_REMINDER);
                $entity->setNotificationTypeArgs(json_encode([
                    'role_id' => FakeRole::defaultRole()->getId(),
                    'cancelled' => false,
                ]) ?: '');
                return $entity;
            }
        );
    }

    public static function emailReminderDefault(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(94857);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }

    public static function emailReminderAdmin(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(29475);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }

    public static function telegramReminderDefault(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(93865);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }

    public static function telegramReminderAdmin(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(10246);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }

    public static function roleReminderDefault(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(23859);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_ROLE_REMINDER);
                $entity->setNotificationTypeArgs(json_encode([
                    'role_id' => FakeRole::defaultRole()->getId(),
                    'cancelled' => false,
                ]) ?: '');
                return $entity;
            }
        );
    }

    public static function roleReminderVorstand(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(92384);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::vorstandUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_ROLE_REMINDER);
                $entity->setNotificationTypeArgs(json_encode([
                    'role_id' => FakeRole::vorstandRole()->getId(),
                    'cancelled' => false,
                ]) ?: '');
                return $entity;
            }
        );
    }

    public static function roleReminderBroken(bool $fresh = false): NotificationSubscription {
        return self::getFake(
            $fresh,
            function () {
                $entity = FakeNotificationSubscription::defaultNotificationSubscription(true);
                $entity->setId(37586);
                $entity->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $entity->setUser(FakeUser::adminUser());
                $entity->setNotificationType(NotificationSubscription::TYPE_ROLE_REMINDER);
                $entity->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '');
                return $entity;
            }
        );
    }
}
