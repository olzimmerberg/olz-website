<?php

// =============================================================================
// Server-spezifische Konfiguration. Überall wo der Code ausgeführt werden soll,
// z.B. Production, Staging, Dev-Server, Integration-Test-Server, muss eine
// Datei `config.php` vorhanden sein, die von hier aus importiert wird.
// =============================================================================

require_once __DIR__.'/../utils/env/EnvUtils.php';

global $_CONFIG;

try {
    $_CONFIG = EnvUtils::fromEnv();
} catch (Exception $exc) {
    echo $exc->getMessage();
    exit(1);
}
