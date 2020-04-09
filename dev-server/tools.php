<?php

require_once __DIR__.'/_/tools/index.php';

run_tools(
    [
        'dump' => [],
        'reset' => [],
        'deploy-cleanup' => [],
        'migrate' => [],
    ],
    $_SERVER,
);
