<?php

require_once __DIR__.'/common.php';
require_once __DIR__.'/../tasks/LogForAnHourTask.php';

function run_hourly() {
    // $job = new LogForAnHourTask();
    // $job->run();

    throw new Exception("run_hourly (src/cron/...) is deprecated!");
}

throttle('cron_hourly', 'run_hourly', [], 30 * 60);
