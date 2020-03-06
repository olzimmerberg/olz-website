<?php

require_once __DIR__.'/common.php';
require_once __DIR__.'/../tasks/LogForAnHourTask.php';

function run_hourly() {
    // $job = new LogForAnHourTask();
    // $job->run();
}

throttle('cron_hourly', 'run_hourly', [], 30 * 60);
