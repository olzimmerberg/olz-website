<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\AuthRequest;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

/**
 * @extends FakeEntity<AuthRequest>
 */
class FakeAuthRequest extends FakeEntity {
    public static function minimal(bool $fresh = false): AuthRequest {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AuthRequest();
                $entity->setId(12);
                $entity->setTimestamp(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): AuthRequest {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AuthRequest();
                $entity->setId(123);
                $entity->setTimestamp(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): AuthRequest {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AuthRequest();
                $entity->setId(1234);
                $entity->setTimestamp(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function defaultAuthRequest(bool $fresh = false): AuthRequest {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AuthRequest();
                $entity->setId(1);
                return $entity;
            }
        );
    }
}
