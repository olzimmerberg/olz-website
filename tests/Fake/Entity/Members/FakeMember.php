<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Members;

use Olz\Entity\Members\Member;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<Member>
 */
class FakeMember extends FakeEntity {
    public static function minimal(bool $fresh = false): Member {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Member();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setIdent('');
                $entity->setData('');
                $entity->setUpdates(null);
                $entity->setUser(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): Member {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Member();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setIdent('');
                $entity->setData('');
                $entity->setUpdates('');
                $entity->setUser(null);
                return $entity;
            },
            function ($entity) {
                $entity->setUser(FakeUser::minimal());
            }
        );
    }

    public static function maximal(bool $fresh = false): Member {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Member();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setIdent('10001234');
                $entity->setData('{"[Id]":"10001234","Benutzer-Id":"minimal-user","Vorname":"Max","Nachname":"User"}');
                $entity->setUpdates('{"Vorname":"Maximal"}');
                return $entity;
            },
            function ($entity) {
                $entity->setUser(FakeUser::maximal());
            }
        );
    }
}
