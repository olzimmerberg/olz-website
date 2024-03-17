<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Entity\Service\Link;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeLinks extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            'minimal',
            $fresh,
            function () {
                $entity = new Link();
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
            'empty',
            $fresh,
            function () {
                $entity = new Link();
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
            'maximal',
            $fresh,
            function () {
                $entity = new Link();
                $entity->setId(1234);
                $entity->setName('Fake Link');
                $entity->setPosition(1234);
                $entity->setUrl('https://ol-z.ch');
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
