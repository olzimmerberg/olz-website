<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Karten;

use Olz\Entity\Karten\Karte;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeKarte extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Karte();
                $entity->setId(12);
                $entity->setName('');
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Karte();
                $entity->setId(123);
                $entity->setKartenNr(0);
                $entity->setName('');
                $entity->setCenterX(null);
                $entity->setCenterY(null);
                $entity->setYear(null);
                $entity->setScale('');
                $entity->setPlace('');
                $entity->setZoom(null);
                $entity->setKind(null);
                $entity->setPreviewImageId('');
                $entity->setOnOff(false);
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Karte();
                $entity->setId(1234);
                $entity->setKartenNr(12);
                $entity->setName('Fake Karte');
                $entity->setCenterX(1200000);
                $entity->setCenterY(120000);
                $entity->setYear(1200);
                $entity->setScale('1:1\'200');
                $entity->setPlace('Fake Place');
                $entity->setZoom(12);
                $entity->setKind('ol');
                $entity->setPreviewImageId('image__________________1.jpg');
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
