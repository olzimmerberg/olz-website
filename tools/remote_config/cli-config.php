<?php

// =============================================================================
// Konfiguration für das Doctrine-Kommandozeilen-Programm.
// =============================================================================

require_once __DIR__.'/doctrine.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
