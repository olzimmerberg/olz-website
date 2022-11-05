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
                $admin_user->setEmail('admin-user@test.olzimmerberg.ch');
                $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
                $admin_user->setPermissions('all');
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
                $vorstand_user->setEmail('vorstand-user@test.olzimmerberg.ch');
                $vorstand_user->setPasswordHash(password_hash('v0r57and', PASSWORD_DEFAULT));
                $vorstand_user->setPermissions('aktuell ftp');
                $vorstand_user->setRoot('vorstand');
                return $vorstand_user;
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
                $default_user->setEmail('default-user@olzimmerberg.ch');
                $default_user->setPasswordHash(password_hash('u53r', PASSWORD_DEFAULT));
                $default_user->setParentUserId(2);
                return $default_user;
            }
        );
    }
}
