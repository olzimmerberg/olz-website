<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\User;

class FakeUsers extends FakeFactory {
    public static function adminUser($fresh = false) {
        return self::getFake(
            'admin_user',
            $fresh,
            function () {
                $admin_user = new User();
                $admin_user->setId(2);
                $admin_user->setFirstName('Admin');
                $admin_user->setLastName('Istrator');
                $admin_user->setUsername('admin');
                $admin_user->setOldUsername('admin-old');
                $admin_user->setEmail('admin-user@staging.olzimmerberg.ch');
                $admin_user->setEmailIsVerified(true);
                $admin_user->setEmailVerificationToken('admintoken');
                $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
                $admin_user->setPermissions('all verified_email');
                $admin_user->setRoot('karten');
                $admin_user->setPhone('+410123456');
                $admin_user->setGender('M');
                $admin_user->setBirthdate(new \DateTime('2000-01-01'));
                $admin_user->setStreet('Data Hwy. 42');
                $admin_user->setPostalCode('19216811');
                $admin_user->setRegion('XX');
                $admin_user->setCity('Test');
                $admin_user->setCountryCode('CH');
                $admin_user->setSiCardNumber('127001');
                $admin_user->setSolvNumber('000ADM');
                $admin_role = FakeRoles::adminRole();
                $admin_user->addRole($admin_role);
                return $admin_user;
            }
        );
    }

    public static function vorstandUser($fresh = false) {
        return self::getFake(
            'vorstand_user',
            $fresh,
            function () {
                $vorstand_user = new User();
                $vorstand_user->setId(3);
                $vorstand_user->setFirstName('Vorstand');
                $vorstand_user->setLastName('Mitglied');
                $vorstand_user->setUsername('vorstand');
                $vorstand_user->setEmail('vorstand-user@staging.olzimmerberg.ch');
                $vorstand_user->setPasswordHash(password_hash('v0r57and', PASSWORD_DEFAULT));
                $vorstand_user->setPermissions('aktuell ftp vorstand_user');
                $vorstand_user->setRoot('vorstand');
                $vorstand_role = FakeRoles::vorstandRole();
                $vorstand_user->addRole($vorstand_role);
                return $vorstand_user;
            }
        );
    }

    public static function parentUser($fresh = false) {
        return self::getFake(
            'parent_user',
            $fresh,
            function () {
                $parent_user = new User();
                $parent_user->setId(4);
                $parent_user->setFirstName('Eltern');
                $parent_user->setLastName('Teil');
                $parent_user->setUsername('parent');
                $parent_user->setEmail('parent-user@staging.olzimmerberg.ch');
                $parent_user->setParentUserId(null);
                $parent_user->setPasswordHash(password_hash('par3n7', PASSWORD_DEFAULT));
                $parent_user->setPermissions('parent');
                $parent_user->setRoot('parent');
                return $parent_user;
            }
        );
    }

    public static function child1User($fresh = false) {
        return self::getFake(
            'child1_user',
            $fresh,
            function () {
                $child1_user = new User();
                $child1_user->setId(5);
                $child1_user->setFirstName('Kind');
                $child1_user->setLastName('Eins');
                $child1_user->setUsername('child1');
                $child1_user->setEmail('child1-user@staging.olzimmerberg.ch');
                $child1_user->setParentUserId(4);
                $child1_user->setPasswordHash(null);
                $child1_user->setPermissions('child1');
                $child1_user->setRoot('child1');
                return $child1_user;
            }
        );
    }

    public static function child2User($fresh = false) {
        return self::getFake(
            'child2_user',
            $fresh,
            function () {
                $child2_user = new User();
                $child2_user->setId(6);
                $child2_user->setFirstName('Kind');
                $child2_user->setLastName('Zwei');
                $child2_user->setUsername('child2');
                $child2_user->setEmail('child2-user@staging.olzimmerberg.ch');
                $child2_user->setParentUserId(4);
                $child2_user->setPasswordHash('');
                $child2_user->setPermissions('child2');
                $child2_user->setRoot('child2');
                return $child2_user;
            }
        );
    }

    public static function defaultUser($fresh = false) {
        return self::getFake(
            'default_user',
            $fresh,
            function () {
                $default_user = new User();
                $default_user->setId(1);
                $default_user->setFirstName('Default');
                $default_user->setLastName('User');
                $default_user->setUsername('user');
                $default_user->setEmail('default-user@staging.olzimmerberg.ch');
                $default_user->setEmailIsVerified(false);
                $default_user->setEmailVerificationToken('defaulttoken');
                $default_user->setPasswordHash(password_hash('u53r', PASSWORD_DEFAULT));
                $default_user->setParentUserId(2);
                return $default_user;
            }
        );
    }
}
