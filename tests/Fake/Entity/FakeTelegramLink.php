<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\TelegramLink;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<TelegramLink>
 */
class FakeTelegramLink extends FakeEntity {
    public static function validPin(bool $fresh = false): TelegramLink {
        return self::getFake(
            $fresh,
            function () {
                global $valid_pin;
                $entity = new TelegramLink();
                $entity->setPin($valid_pin);
                $entity->setPinExpiresAt(new \DateTime('2020-03-13 19:35:00')); // in 5 minutes
                $entity->setUser(FakeUser::defaultUser());
                $entity->setTelegramChatId('99999');
                $entity->setTelegramChatState(['state' => 'valid']);
                $entity->setTelegramUserId('99');
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:25:00')); // 5 minutes ago
                $entity->setLinkedAt(null);
                return $entity;
            }
        );
    }

    public static function expiredPin(bool $fresh = false): TelegramLink {
        return self::getFake(
            $fresh,
            function () {
                global $expired_pin;
                $entity = new TelegramLink();
                $entity->setPin($expired_pin);
                $entity->setPinExpiresAt(new \DateTime('2020-03-13 19:25:00')); // 5 minutes ago
                $entity->setUser(FakeUser::vorstandUser());
                $entity->setTelegramChatId('88888');
                $entity->setTelegramChatState(['state' => 'expired']);
                $entity->setTelegramUserId('88');
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:15:00')); // 15 minutes ago
                $entity->setLinkedAt(null);
                return $entity;
            }
        );
    }

    public static function null(bool $fresh = false): TelegramLink {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TelegramLink();
                $entity->setPin(null);
                $entity->setPinExpiresAt(null);
                $entity->setUser(null);
                $entity->setTelegramChatId(null);
                $entity->setTelegramChatState([]);
                $entity->setTelegramUserId(null);
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:25:00')); // 5 minutes ago
                $entity->setLinkedAt(null);
                return $entity;
            }
        );
    }
}
