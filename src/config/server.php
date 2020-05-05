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
if (!is_file($config_path)) {
    die('Config file not found');
}
$_CONFIG = new ServerConfig();
require_once $config_path;
