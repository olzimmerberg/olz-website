<?php

// =============================================================================
// Konfiguration für die Benützung von Doctrine im Code.
// =============================================================================

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once 'vendor/autoload.php';
require_once __DIR__.'/database.php';

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    [__DIR__.'/../model'],
    $isDevMode,
    null,
    null,
    false,
);
$conn = [
    'driver' => 'pdo_mysql',
    'dbname' => $MYSQL_SCHEMA,
    'user' => $MYSQL_USERNAME,
    'password' => $MYSQL_PASSWORD,
    'host' => $MYSQL_SERVER,
];

$entityManager = EntityManager::create($conn, $config);
