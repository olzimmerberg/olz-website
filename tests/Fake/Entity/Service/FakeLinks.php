<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Entity\Service\Link;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

class FakeLinks extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Link();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setName('Fake Link');
                $entity->setPosition(12);
                $entity->setUrl('https://ol-z.ch');
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Link();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setName('Fake Link');
                $entity->setPosition(123);
                $entity->setUrl('https://ol-z.ch');
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Link();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setName('Fake Link');
                $entity->setPosition(1234);
                $entity->setUrl('https://ol-z.ch');
                return $entity;
            }
        );
    }
}
