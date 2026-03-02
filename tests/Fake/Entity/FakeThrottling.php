<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\Throttling;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

/**
 * @extends FakeEntity<Throttling>
 */
class FakeThrottling extends FakeEntity {
    public static function minimal(bool $fresh = false): Throttling {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Throttling();
                $entity->setId(12);
                $entity->setEventName('required');
                $entity->setLastOccurrence(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): Throttling {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Throttling();
                $entity->setId(123);
                $entity->setEventName('');
                $entity->setLastOccurrence(new \DateTime('0000-00-00 00:00:00'));
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): Throttling {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Throttling();
                $entity->setId(1234);
                $entity->setEventName('maximal-event');
                $entity->setLastOccurrence(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }
}
