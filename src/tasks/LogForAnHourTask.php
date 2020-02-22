<?php

require_once __DIR__.'/common.php';

class LogForAnHourTask extends BackgroundTask {
    protected static function get_ident() {
        return "LogForAnHour";
    }

    protected function run_specific_task() {
        for ($i = 0; $i < 360; $i++) {
            $time = date('H:i:s');
            $this->log_info("It is {$time}");
            sleep(10);
        }
        $this->log_info("Successfully wasted an hour!");
    }
}
