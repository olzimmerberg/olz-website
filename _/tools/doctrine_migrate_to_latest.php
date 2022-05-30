<?php

$cwd = getcwd();
$target_dir = realpath(__DIR__."/../config");
chdir($target_dir);

global $argv, $_SERVER;
$argv = ['doctrine-migrations', 'migrate', 'latest', '--no-interaction'];
$_SERVER['args'] = $argv;
$_SERVER['argv'] = $argv;
$doctrine_migrations_path = $target_dir.'/vendor/doctrine/migrations/bin/doctrine-migrations.php';
require $doctrine_migrations_path;
