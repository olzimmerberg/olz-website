<?php

// =============================================================================
// Server-spezifische Konfiguration. Überall wo der Code ausgeführt werden soll,
// z.B. Production, Staging, Dev-Server, Integration-Test-Server, muss eine
// Datei `config.php` vorhanden sein, die von hier aus importiert wird.
// =============================================================================

use Olz\Utils\EnvUtils;

global $_CONFIG;

try {
    $_CONFIG = EnvUtils::fromEnv(); // $_ENV is a PHP predefined variable, unfortunately :/
} catch (\Exception $exc) {
    echo $exc->getMessage();
    exit(1);
}
