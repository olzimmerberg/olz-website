<?php

// =============================================================================
// Server-spezifische Konfiguration. Überall wo der Code ausgeführt werden soll,
// z.B. Production, Staging, Dev-Server, Integration-Test-Server, muss eine
// Datei `config.php` vorhanden sein, die von hier aus importiert wird.
// =============================================================================

class ServerConfig {
    public $unlimited_cron = false;
}

$config_path = $_SERVER['DOCUMENT_ROOT'].'/config.php';
if (!isset($_SERVER['DOCUMENT_ROOT']) || !$_SERVER['DOCUMENT_ROOT']) {
    // e.g. for doctrine cli-config.php
    $config_path = __DIR__.'/../../dev-server/config.php';
}
$first_arg = $_SERVER['argv'][0];
$is_phpunit = preg_match('/phpunit$/', $first_arg);
$last_arg = $_SERVER['argv'][count($_SERVER['argv']) - 1];
$executing_unit_tests = preg_match('/unit_tests$/', $last_arg);
if ($is_phpunit && $executing_unit_tests) {
    throw new \Exception('Unit tests should never import config/*');
}
if (!is_file($config_path)) {
    echo 'Config file not found';
    exit(1);
}
$_CONFIG = new ServerConfig();
require_once $config_path;
