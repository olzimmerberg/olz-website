<?php

require_once __DIR__.'/_/tools/index.php';

run_tools(
    [
        'reset' => [],
        'dump' => [],
        'get-database-backup' => [],
        'deploy-cleanup' => [],
        'migrate' => [],
        'backup-monitoring' => [],
    ],
    $_SERVER,
);
