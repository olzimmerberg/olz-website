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
                $entity->setCenterX(null);
                $entity->setCenterY(null);
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
                $entity->setCenterX(null);
                $entity->setCenterY(null);
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
                $entity->setCenterX(1200000);
                $entity->setCenterY(120000);
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
