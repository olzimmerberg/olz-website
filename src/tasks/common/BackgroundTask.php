<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../config/paths.php';

abstract class BackgroundTask {
    use Psr\Log\LoggerAwareTrait;

    public function __construct($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    protected function setup() {
    }

    protected function teardown() {
    }

    public function setDefaultFileLogger() {
        $log_path = $this->generateLogPath();
        if (!is_dir(dirname($log_path))) {
            mkdir(dirname($log_path), 0777, true);
        }
        $logger = new Logger($this->getIdent());
        $logger->pushHandler(new StreamHandler($log_path, Logger::INFO));
        $this->setLogger($logger);
    }

    public function run() {
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
    }

    public function generateLogPath() {
        global $data_path;
        $timestamp = $this->dateUtils->getCurrentDateInFormat('Y-m-d_H_i_s');
        return "{$data_path}tasks/log_{$timestamp}_{$this->getIdent()}.txt";
    }

    abstract protected static function getIdent();

    abstract protected function runSpecificTask();
}
