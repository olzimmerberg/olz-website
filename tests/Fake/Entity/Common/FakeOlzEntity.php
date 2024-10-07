<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Olz\Entity\Common\OlzEntity;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;

class FakeOlzEntity {
    public static function minimal(OlzEntity $entity): void {
        $entity->setOnOff(1);
        $entity->setOwnerUser(null);
        $entity->setOwnerRole(null);
        $entity->setCreatedAt(new \DateTime('1970-01-01 00:00:00'));
        $entity->setCreatedByUser(null);
        $entity->setLastModifiedAt(new \DateTime('1970-01-01 00:00:00'));
        $entity->setLastModifiedByUser(null);
    }

    public static function empty(OlzEntity $entity): void {
        $entity->setOnOff(0);
        $entity->setOwnerUser(null);
        $entity->setOwnerRole(null);
        $entity->setCreatedAt(new \DateTime('1970-01-01 00:00:00'));
        $entity->setCreatedByUser(null);
        $entity->setLastModifiedAt(new \DateTime('1970-01-01 00:00:00'));
        $entity->setLastModifiedByUser(null);
    }

    public static function maximal(OlzEntity $entity): void {
        $entity->setOnOff(1);
        $entity->setOwnerUser(FakeUser::defaultUser());
        $entity->setOwnerRole(FakeRole::defaultRole());
        $entity->setCreatedAt(new \DateTime('2006-01-13 18:43:36'));
        $entity->setCreatedByUser(FakeUser::defaultUser());
        $entity->setLastModifiedAt(new \DateTime('2020-03-13 18:43:36'));
        $entity->setLastModifiedByUser(FakeUser::defaultUser());
    }
}
