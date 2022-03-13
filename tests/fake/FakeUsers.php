<?php

require_once __DIR__.'/../../src/model/User.php';
require_once __DIR__.'/FakeFactory.php';

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
                $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
                $admin_user->setZugriff('all');
                $admin_user->setRoot('karten');
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
                $vorstand_user->setPasswordHash(password_hash('v0r57and', PASSWORD_DEFAULT));
                $vorstand_user->setZugriff('aktuell ftp');
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
                return $default_user;
            }
        );
    }
}
