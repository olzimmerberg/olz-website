<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../model/UserRole.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$users_roles_table = new DbTable('UserRole', 'users_roles', [
    new DbInteger('user', 'user', ['primary_key' => true]),
    new DbInteger('role', 'role', ['primary_key' => true]),
]);
