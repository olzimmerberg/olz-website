<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLocation;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeTerminLocation extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            'minimal',
            $fresh,
            function () {
                $entity = new TerminLocation();
                $entity->setId(12);
                $entity->setName("Fake title");
                $entity->setDetails("");
                $entity->setLatitude(0);
                $entity->setLongitude(0);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            'empty',
            $fresh,
            function () {
                $entity = new TerminLocation();
                $entity->setId(123);
                $entity->setName("Cannot be empty");
                $entity->setDetails("");
                $entity->setLatitude(0);
                $entity->setLongitude(0);
                $entity->setImageIds([]);
                $entity->setOnOff(false);
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            'maximal',
            $fresh,
            function () {
                $entity = new TerminLocation();
                $entity->setId(1234);
                $entity->setName("Fake title");
                $entity->setDetails("Fake content");
                $entity->setLatitude(47.2790953);
                $entity->setLongitude(8.5591936);
                $entity->setImageIds(['image__________________1.jpg', 'image__________________2.png']);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
