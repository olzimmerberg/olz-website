<?php

require_once __DIR__.'/common.php';
require_once __DIR__.'/../tasks/SyncSolvTask.php';

function run_daily() {
    $job = new SyncSolvTask();
    $job->run();
}

throttle('cron_daily', 'run_daily', [], 12 * 3600);
// run_daily();
