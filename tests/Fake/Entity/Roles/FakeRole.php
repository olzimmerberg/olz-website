<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Roles;

use Olz\Entity\Roles\Role;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<Role>
 */
class FakeRole extends FakeEntity {
    public static function minimal(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setUsername('');
                $entity->setOldUsername(null);
                $entity->setName('');
                $entity->setDescription('');
                $entity->setGuide('');
                $entity->setParentRoleId(null);
                $entity->setPositionWithinParent(null);
                $entity->setFeaturedPosition(null);
                $entity->setCanHaveChildRoles(false);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setUsername('');
                $entity->setOldUsername('');
                $entity->setName('');
                $entity->setDescription('');
                $entity->setGuide('');
                $entity->setParentRoleId(null);
                $entity->setPositionWithinParent(0.0);
                $entity->setFeaturedPosition(null);
                $entity->setCanHaveChildRoles(false);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setUsername('test-role');
                $entity->setOldUsername('old-test-role');
                $entity->setName('Test Role');
                $entity->setDescription('Description Test Role');
                $entity->setGuide('Just do it!');
                $entity->setParentRoleId(3);
                $entity->setPositionWithinParent(2.0);
                $entity->setFeaturedPosition(6);
                $entity->setCanHaveChildRoles(true);
                return $entity;
            },
            function ($entity) {
                $entity->addUser(FakeUser::maximal());
                $entity->addUser(FakeUser::empty());
                $entity->addUser(FakeUser::minimal());
            }
        );
    }

    public static function adminRole(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::minimal($entity);
                $entity->setId(2);
                $entity->setUsername('admin_role');
                $entity->setName('Administrator');
                $entity->setPermissions('all');
                $entity->setParentRoleId(null);
                return $entity;
            },
            function ($entity) {
                $entity->addUser(FakeUser::adminUser());
            }
        );
    }

    public static function vorstandRole(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::minimal($entity);
                $entity->setId(3);
                $entity->setUsername('vorstand_role');
                $entity->setName('Vorstand');
                $entity->setDescription("Description {$entity->getName()}");
                $entity->setGuide("Guide {$entity->getName()}");
                $entity->setPermissions('aktuell ftp vorstand_role');
                $entity->setParentRoleId(null);
                $entity->setPositionWithinParent(0.0);
                $entity->setFeaturedPosition(null);
                $entity->setCanHaveChildRoles(true);
                return $entity;
            },
            function ($entity) {
                $entity->addUser(FakeUser::vorstandUser());
            }
        );
    }

    public static function subVorstandRole(bool $fresh = false, int $degree = 1): Role {
        return self::getFake(
            $fresh,
            function () use ($degree) {
                $entity = new Role();
                FakeOlzEntity::minimal($entity);
                $entity->setId(intval(str_repeat('3', $degree + 1)));
                $entity->setUsername(str_repeat('sub_', $degree).'vorstand_role');
                $entity->setName(str_repeat('Sub-', $degree).'Vorstand');
                $entity->setDescription("Description {$entity->getName()}");
                $entity->setGuide("Guide {$entity->getName()}");
                $entity->setPermissions(str_repeat('sub_', $degree).'vorstand_role ftp');
                $entity->setParentRoleId(intval(str_repeat('3', $degree)));
                $entity->setPositionWithinParent(0.0);
                $entity->setFeaturedPosition(null);
                $entity->setCanHaveChildRoles(true);
                return $entity;
            }
        );
    }

    public static function defaultRole(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::minimal($entity);
                $entity->setId(1);
                $entity->setUsername('role');
                $entity->setName('Default');
                $entity->setPermissions('');
                $entity->setParentRoleId(null);
                return $entity;
            }
        );
    }

    public static function someRole(bool $fresh = false): Role {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                FakeOlzEntity::minimal($entity);
                $entity->setId(1);
                $entity->setUsername('somerole');
                $entity->setOldUsername('somerole-old');
                $entity->setName('Some Role');
                $entity->setPermissions('');
                $entity->setParentRoleId(null);
                $entity->addUser(FakeUser::adminUser());
                $entity->addUser(FakeUser::vorstandUser());
                return $entity;
            }
        );
    }
}
