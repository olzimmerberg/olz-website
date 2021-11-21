<?php

require_once __DIR__.'/common/BackgroundTask.php';

class LogForAnHourTask extends BackgroundTask {
    protected static function getIdent() {
        return "LogForAnHour";
    }

    protected function runSpecificTask() {
        $success = set_time_limit(4000);
        if ($success) {
            $this->logger->info("Successfully set time limit");
        } else {
            $this->logger->warning("Could not set time limit. Let's hope for the best :/");
        }
        for ($i = 0; $i < 60; $i++) {
            $time = $this->dateUtils->getCurrentDateInFormat('H:i:s');
            $this->logger->info("It is {$time}");
            sleep(10);
        }
        $this->logger->info("Successfully wasted an hour!");
    }
}
