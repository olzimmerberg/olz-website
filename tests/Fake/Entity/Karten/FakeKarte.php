<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Karten;

use Olz\Entity\Karten\Karte;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<Karte>
 */
class FakeKarte extends FakeEntity {
    public static function minimal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Karte();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setKartenNr(null);
                $entity->setName('');
                $entity->setLatitude(null);
                $entity->setLongitude(null);
                $entity->setYear(null);
                $entity->setScale(null);
                $entity->setPlace(null);
                $entity->setZoom(null);
                $entity->setKind(null);
                $entity->setPreviewImageId(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Karte();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setKartenNr(0);
                $entity->setName('');
                $entity->setLatitude(null);
                $entity->setLongitude(null);
                $entity->setYear(null);
                $entity->setScale('');
                $entity->setPlace('');
                $entity->setZoom(null);
                $entity->setKind(null);
                $entity->setPreviewImageId('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Karte();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setKartenNr(12);
                $entity->setName('Fake Karte');
                $entity->setLatitude(47.2);
                $entity->setLongitude(8.6);
                $entity->setYear('1200');
                $entity->setScale('1:1\'200');
                $entity->setPlace('Fake Place');
                $entity->setZoom(12);
                $entity->setKind('ol');
                $entity->setPreviewImageId('image__________________1.jpg');
                return $entity;
            }
        );
    }
}
