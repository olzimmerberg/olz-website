<?php

require_once __DIR__.'/common.php';

class LogForAnHourTask extends BackgroundTask {
    protected static function get_ident() {
        return "LogForAnHour";
    }

    protected function run_specific_task() {
        $success = set_time_limit(4000);
        if ($success) {
            $this->logger->info("Successfully set time limit");
        } else {
            $this->logger->warning("Could not set time limit. Let's hope for the best :/");
        }
        for ($i = 0; $i < 360; $i++) {
            $time = date('H:i:s');
            $this->logger->info("It is {$time}");
            sleep(10);
        }
        $this->logger->info("Successfully wasted an hour!");
    }
}
