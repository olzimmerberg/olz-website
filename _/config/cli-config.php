<?php

// =============================================================================
// Konfiguration für das Doctrine-Kommandozeilen-Programm.
// =============================================================================

use Olz\Utils\DbUtils;

$entityManager = DbUtils::fromEnv()->getEntityManager();

if (!$entityManager) {
    return null;
}

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
