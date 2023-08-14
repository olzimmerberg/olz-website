<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\Role;

class FakeRoles extends FakeFactory {
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
                $some_role->addUser(FakeUsers::adminUser());
                $some_role->addUser(FakeUsers::vorstandUser());
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
                $some_role->addUser(FakeUsers::adminUser());
                $some_role->addUser(FakeUsers::vorstandUser());
                return $some_role;
            }
        );
    }
}
