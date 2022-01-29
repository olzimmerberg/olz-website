<?php

// =============================================================================
// Doctrine-Import
// =============================================================================

global $doctrine_model_folders;

require_once __DIR__.'/vendor/autoload.php';

function get_model_folder($path) {
    if (!is_dir($path)) {
        throw new Exception("Model folder does not exist: {$path}");
    }
    $realpath = realpath($path);
    if (!$realpath) {
        throw new Exception("Could not get realpath of model folder: {$path}");
    }
    return $realpath;
}

$doctrine_model_folders = [
    get_model_folder(__DIR__.'/../model'),
    get_model_folder(__DIR__.'/../news/model'),
    get_model_folder(__DIR__.'/../termine/model'),
];
