<?php

// =============================================================================
// Konfiguration für das Doctrine-Kommandozeilen-Programm.
// =============================================================================

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    [__DIR__.'/../../src/Entity'],
    $isDevMode,
    null,
    null,
    false,
);
$password = getenv('DOCTRINE_CONNECTION_PASSWORD');
$conn = [
    'driver' => 'pdo_mysql',
    'dbname' => 'olz_prod',
    'user' => 'olz',
    'password' => $password,
    'host' => '219.hosttech.eu',
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($conn, $config);

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
