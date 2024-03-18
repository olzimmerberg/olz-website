<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Roles;

use Olz\Entity\Roles\Role;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\FakeUser;

class FakeRoles extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            'minimal',
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
            'empty',
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
            'maximal',
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
                $entity->setParentRoleId(8);
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
            'admin_role',
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
            'vorstand_role',
            $fresh,
            function () {
                $vorstand_role = new Role();
                $vorstand_role->setId(3);
                $vorstand_role->setUsername('vorstand_role');
                $vorstand_role->setName('Vorstand');
                $vorstand_role->setPermissions('aktuell ftp vorstand_role');
                return $vorstand_role;
            }
        );
    }

    public static function defaultRole($fresh = false) {
        return self::getFake(
            'default_role',
            $fresh,
            function () {
                $default_role = new Role();
                $default_role->setId(1);
                $default_role->setUsername('role');
                $default_role->setName('Default');
                $default_role->setPermissions('');
                return $default_role;
            }
        );
    }

    public static function someRole($fresh = false) {
        return self::getFake(
            'some_role',
            $fresh,
            function () {
                $some_role = new Role();
                $some_role->setId(1);
                $some_role->setUsername('somerole');
                $some_role->setName('Some Role');
                $some_role->setPermissions('');
                $some_role->addUser(FakeUser::adminUser());
                $some_role->addUser(FakeUser::vorstandUser());
                return $some_role;
            }
        );
    }

    public static function someOldRole($fresh = false) {
        return self::getFake(
            'some_old_role',
            $fresh,
            function () {
                $some_role = new Role();
                $some_role->setId(2);
                $some_role->setUsername('somerole-old');
                $some_role->setName('Some Old Role');
                $some_role->setPermissions('');
                $some_role->addUser(FakeUser::adminUser());
                $some_role->addUser(FakeUser::vorstandUser());
                return $some_role;
            }
        );
    }
}
