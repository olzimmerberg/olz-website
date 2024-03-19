<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Roles;

use Olz\Entity\Roles\Role;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\FakeUser;

class FakeRole extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
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
                $entity = new Role();
                $entity->setId(123);
                $entity->setUsername('');
                $entity->setOldUsername('');
                $entity->setName('');
                $entity->setTitle('');
                $entity->setDescription('');
                $entity->setGuide('');
                $entity->setParentRoleId(null);
                $entity->setIndexWithinParent(-1);
                $entity->setFeaturedIndex(null);
                $entity->setCanHaveChildRoles(false);
                $entity->setOnOff(false);
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                $entity->setId(1234);
                $entity->setUsername('test-role');
                $entity->setOldUsername('old-test-role');
                $entity->setName('Test Role');
                $entity->setTitle('Title Test Role');
                $entity->setDescription('Description Test Role');
                $entity->setGuide('Just do it!');
                $entity->setParentRoleId(3);
                $entity->setIndexWithinParent(2);
                $entity->setFeaturedIndex(6);
                $entity->setCanHaveChildRoles(true);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }

    public static function adminRole($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $admin_role = new Role();
                $admin_role->setId(2);
                $admin_role->setUsername('admin_role');
                $admin_role->setName('Administrator');
                $admin_role->setPermissions('all');
                return $admin_role;
            }
        );
    }

    public static function vorstandRole($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                $entity->setId(3);
                $entity->setUsername('vorstand_role');
                $entity->setName('Vorstand');
                $entity->setPermissions('aktuell ftp vorstand_role');
                return $entity;
            }
        );
    }

    public static function subVorstandRole($fresh = false, $degree = 1) {
        return self::getFake(
            $fresh,
            function () use ($degree) {
                $entity = new Role();
                $entity->setId(intval(str_repeat('3', $degree + 1)));
                $entity->setUsername(str_repeat('sub_', $degree + 1).'vorstand_role');
                $entity->setName(str_repeat('Sub-', $degree + 1).'Vorstand');
                $entity->setPermissions(str_repeat('sub_', $degree + 1).'vorstand_role ftp');
                $entity->setParentRoleId(intval(str_repeat('3', $degree)));
                return $entity;
            }
        );
    }

    public static function defaultRole($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                $entity->setId(1);
                $entity->setUsername('role');
                $entity->setName('Default');
                $entity->setPermissions('');
                return $entity;
            }
        );
    }

    public static function someRole($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                $entity->setId(1);
                $entity->setUsername('somerole');
                $entity->setName('Some Role');
                $entity->setPermissions('');
                $entity->addUser(FakeUser::adminUser());
                $entity->addUser(FakeUser::vorstandUser());
                return $entity;
            }
        );
    }

    public static function someOldRole($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Role();
                $entity->setId(2);
                $entity->setUsername('somerole-old');
                $entity->setName('Some Old Role');
                $entity->setPermissions('');
                $entity->addUser(FakeUser::adminUser());
                $entity->addUser(FakeUser::vorstandUser());
                return $entity;
            }
        );
    }
}
