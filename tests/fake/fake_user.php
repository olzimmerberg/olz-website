<?php

require_once __DIR__.'/../../src/model/User.php';
require_once __DIR__.'/FakeFactory.php';

function get_fake_user() {
    $user = new User();
    $user->setId(1);
    $user->setUsername('user');
    $user->setPasswordHash(password_hash('u53r', PASSWORD_DEFAULT));
    return $user;
}

class FakeUsers extends FakeFactory {
    public static function adminUser($fresh = false) {
        return self::getFake(
            'admin_user',
            $fresh,
            function () {
                $admin_user = get_fake_user();
                $admin_user->setId(2);
                $admin_user->setUsername('admin');
                $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
                $admin_user->setZugriff('all');
                $admin_user->setRoot('karten');
                return $admin_user;
            }
        );
    }
}
