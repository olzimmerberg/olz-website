<?php

namespace Olz\Tasks;

use Olz\Tasks\Common\BackgroundTask;

class LogForAnHourTask extends BackgroundTask {
    protected static function getIdent() {
        return "LogForAnHour";
    }

    protected function runSpecificTask() {
        $success = set_time_limit(4000);
        if ($success) {
            $this->log()->info("Successfully set time limit");
        } else {
            $this->log()->warning("Could not set time limit. Let's hope for the best :/");
        }
        for ($i = 0; $i < 360; $i++) {
            $time = $this->dateUtils->getCurrentDateInFormat('H:i:s');
            $this->log()->info("It is {$time}");
            sleep(10);
        }
        $this->log()->info("Successfully wasted an hour!");
    }
}
