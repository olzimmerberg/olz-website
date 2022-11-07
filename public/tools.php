<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../_/tools/index.php';

run_tools(
    [
        'reset-content' => [],
        'reset-structure' => [],
        'full-reset' => [],
        'dump' => [],
        'get-database-backup' => [],
        'get-id-algos' => [],
        'migrate' => [],
        'backup-monitoring' => [],
        'logs-monitoring' => [],
    ],
    $_SERVER,
);
