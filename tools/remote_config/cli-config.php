<?php

// =============================================================================
// Konfiguration für das Doctrine-Kommandozeilen-Programm.
// =============================================================================

global $entityManager;

require_once __DIR__.'/doctrine_db.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
