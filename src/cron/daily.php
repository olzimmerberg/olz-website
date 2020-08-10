<?php

require_once __DIR__.'/common.php';
require_once __DIR__.'/../config/doctrine.php';
require_once __DIR__.'/../fetchers/SolvFetcher.php';
require_once __DIR__.'/../tasks/SyncSolvTask.php';

function run_daily() {
    global $entityManager;
    $solv_fetcher = new SolvFetcher();
    $job = new SyncSolvTask($entityManager, $solv_fetcher);
    $job->setDefaultFileLogger();
    $job->run();
}

throttle('cron_daily', 'run_daily', [], 12 * 3600);
