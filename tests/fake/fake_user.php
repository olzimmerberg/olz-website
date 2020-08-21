<?php

require_once __DIR__.'/../../src/model/User.php';

function get_fake_user() {
    $user = new User();
    $user->setUsername('user');
    $user->setPasswordHash(password_hash('u53r', PASSWORD_DEFAULT));
    return $user;
}
