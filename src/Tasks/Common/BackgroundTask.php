<?php

namespace Olz\Tasks\Common;

use Olz\Utils\LogsUtils;

abstract class BackgroundTask {
    use \Psr\Log\LoggerAwareTrait;

    public function __construct($dateUtils, $envUtils) {
        $this->dateUtils = $dateUtils;
        $this->envUtils = $envUtils;
        $logger = $this->envUtils->getLogsUtils()->getLogger("Task:{$this->getIdent()}");
        $this->setLogger($logger);
    }

    protected function setup() {
    }

    protected function teardown() {
    }

    public function run() {
        LogsUtils::activateLogger($this->logger);

        $this->logger->info("Setup task {$this->getIdent()}...");
        $this->setup();
        try {
            $this->logger->info("Running task {$this->getIdent()}...");
            $this->runSpecificTask();
            $this->logger->info("Finished task {$this->getIdent()}.");
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->logger->error("Error running task {$this->getIdent()}: {$message}.", [$exc]);
        } finally {
            $this->logger->info("Teardown task {$this->getIdent()}...");
            $this->teardown();
        }

        LogsUtils::deactivateLogger($this->logger);
    }

    abstract protected static function getIdent();

    abstract protected function runSpecificTask();
}
