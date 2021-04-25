<?php

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../utils/env/EnvUtils.php';

abstract class BackgroundTask {
    use Psr\Log\LoggerAwareTrait;

    public function __construct($dateUtils, $envUtils) {
        $this->dateUtils = $dateUtils;
        $this->envUtils = $envUtils;
        $logger = $this->envUtils->getLogger("Task:{$this->getIdent()}");
        $this->setLogger($logger);
    }

    protected function setup() {
    }

    protected function teardown() {
    }

    public function run() {
        EnvUtils::activateLogger($this->logger);

        $this->logger->info("Setup task {$this->getIdent()}...");
        $this->setup();
        try {
            $this->logger->info("Running task {$this->getIdent()}...");
            $this->runSpecificTask();
            $this->logger->info("Finished task {$this->getIdent()}.");
        } catch (Exception $exc) {
            $this->logger->error("Error running task {$this->getIdent()}.", [$exc]);
        } finally {
            $this->logger->info("Teardown task {$this->getIdent()}...");
            $this->teardown();
        }

        EnvUtils::deactivateLogger($this->logger);
    }

    abstract protected static function getIdent();

    abstract protected function runSpecificTask();
}
