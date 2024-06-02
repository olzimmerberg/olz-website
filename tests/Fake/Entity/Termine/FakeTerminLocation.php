<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLocation;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<TerminLocation>
 */
class FakeTerminLocation extends FakeEntity {
    public static function minimal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLocation();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setName("Fake title");
                $entity->setDetails("");
                $entity->setLatitude(0);
                $entity->setLongitude(0);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLocation();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setName("Cannot be empty");
                $entity->setDetails("");
                $entity->setLatitude(0);
                $entity->setLongitude(0);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLocation();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setName("Fake title");
                $entity->setDetails("Fake content");
                $entity->setLatitude(47.2790953);
                $entity->setLongitude(8.5591936);
                $entity->setImageIds(['image__________________1.jpg', 'image__________________2.png']);
                return $entity;
            }
        );
    }
}
