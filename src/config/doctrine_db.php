<?php

// =============================================================================
// Konfiguration für die Benützung von Doctrine im Code.
// =============================================================================

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

global $_CONFIG, $doctrine_model_folders, $entityManager;

require_once __DIR__.'/server.php';
require_once __DIR__.'/database.php';
require_once __DIR__.'/doctrine.php';

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    $doctrine_model_folders,
    $isDevMode,
    null,
    null,
    false,
);
$conn = [
    'driver' => 'pdo_mysql',
    'dbname' => $_CONFIG->getMysqlSchema(),
    'user' => $_CONFIG->getMysqlUsername(),
    'password' => $_CONFIG->getMysqlPassword(),
    'host' => $_CONFIG->getMysqlServer(),
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($conn, $config);
