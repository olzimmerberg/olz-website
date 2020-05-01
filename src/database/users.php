<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../model/User.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$users_table = new DbTable('User', 'users', [
    new DbInteger('id', 'id', ['primary_key' => true]),
    new DbString('username', 'username', []),
    new DbString('old_username', 'old_username', []),
    new DbString('password', 'password', []),
    new DbString('email', 'email', []),
    new DbString('first_name', 'first_name', []),
    new DbString('last_name', 'last_name', []),
    new DbString('zugriff', 'zugriff', []),
    new DbString('root', 'root', []),
    new DbBoolean('member', 'member', []),
]);
