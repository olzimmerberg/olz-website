<?php

namespace Olz\Tasks\Common;

use Olz\Utils\LogsUtils;
use Olz\Utils\WithUtilsTrait;

abstract class BackgroundTask {
    use WithUtilsTrait;

    protected function setup() {
    }

    protected function teardown() {
    }

    public function run() {
        LogsUtils::activateLogger($this->log());

        $this->log()->info("Setup task {$this->getIdent()}...");
        $this->setup();
        try {
            $this->log()->info("Running task {$this->getIdent()}...");
            $this->runSpecificTask();
            $this->log()->info("Finished task {$this->getIdent()}.");
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->error("Error running task {$this->getIdent()}: {$message}.", [$exc]);
        } finally {
            $this->log()->info("Teardown task {$this->getIdent()}...");
            $this->teardown();
        }

        LogsUtils::deactivateLogger($this->log());
    }

    abstract protected static function getIdent();

    abstract protected function runSpecificTask();
}
