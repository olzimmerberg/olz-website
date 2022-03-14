<?php

require_once __DIR__.'/_/tools/index.php';

run_tools(
    [
        'reset' => [],
        'full_reset' => [],
        'dump' => [],
        'get-database-backup' => [],
        'migrate' => [],
        'backup-monitoring' => [],
        'logs-monitoring' => [],
    ],
    $_SERVER,
);
