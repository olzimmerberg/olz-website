<?php

// =============================================================================
// Konfiguration der Datenbank-Verbindung
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
    'dbname' => 'ch279178_olz_prod',
    'user' => 'ch279178_olz_prod',
    'password' => $password,
    'host' => 'lx7.hoststar.hosting',
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($conn, $config);
