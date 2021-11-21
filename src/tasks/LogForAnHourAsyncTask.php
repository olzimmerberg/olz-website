<?php

require_once __DIR__.'/common/AsyncTask.php';

class LogForAnHourAsyncTask extends AsyncTask {
    protected static function getIdent() {
        return "LogForAnHourAsync";
    }

    protected function getContinuationUrl() {
        $base_url = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();
        $task_id = $this->getTaskId();
        return "{$base_url}{$code_href}api/index.php/continueAsyncTask?taskId={$task_id}";
    }

    protected function processSpecificTask() {
        $state = $this->getState();
        $step = $state['step'] ?? 0;
        if ($step >= 60) {
            $this->logger->info("Successfully wasted an hour!");
            return $this->finish();
        }
        $time = $this->dateUtils->getCurrentDateInFormat('H:i:s');
        $this->logger->info("It is {$time}");
        sleep(10);
        $state['step'] = $step + 1;
        $this->setState($state);
        return $this->continue();
    }
}
