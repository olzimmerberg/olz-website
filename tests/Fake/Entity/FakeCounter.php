<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\Counter;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

/**
 * @extends FakeEntity<Counter>
 */
class FakeCounter extends FakeEntity {
    public static function minimal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Counter();
                $entity->setId(12);
                $entity->setDateRange('2020-03');
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Counter();
                $entity->setId(123);
                $entity->setDateRange('2020-03');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Counter();
                $entity->setId(1234);
                $entity->setDateRange('2020');
                return $entity;
            }
        );
    }

    public static function defaultCounter(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Counter();
                $entity->setId(1);
                return $entity;
            }
        );
    }
}
