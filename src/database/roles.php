<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../model/Role.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$roles_table = new DbTable('Role', 'roles', [
    new DbInteger('id', 'id', ['primary_key' => true]),
    new DbString('username', 'username', []),
    new DbString('old_username', 'old_username', []),
    new DbString('name', 'name', []),
    new DbInteger('parent_role', 'parent_role', []),
    new DbBoolean('featured', 'featured', []),
]);
