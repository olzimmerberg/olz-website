<?php

// =============================================================================
// Konfiguration für das Doctrine-Kommandozeilen-Programm.
// =============================================================================

use Olz\Utils\DbUtils;

$entityManager = DbUtils::fromEnv()->getEntityManager();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
