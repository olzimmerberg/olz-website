<?php

use Monolog\ErrorHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

require_once __DIR__.'/../../config/vendor/autoload.php';

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
        global $data_path;
        require_once __DIR__.'/../../config/paths.php';
        $log_path = "{$data_path}logs/";
        if (!is_dir(dirname($log_path))) {
            mkdir(dirname($log_path), 0777, true);
        }
        $logger = new Logger($this->getIdent());
        $logger->pushHandler(new RotatingFileHandler("{$log_path}merged.log", 366));
        $this->setLogger($logger);
    }

    public function run() {
        $handler = new ErrorHandler($this->logger);
        $handler->registerErrorHandler();
        $handler->registerExceptionHandler();

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

        restore_error_handler();
        restore_exception_handler();
    }

    public function generateLogPath() {
        global $data_path;
        require __DIR__.'/../../config/paths.php';
        $timestamp = $this->dateUtils->getCurrentDateInFormat('Y-m-d_H_i_s');
        return "{$data_path}tasks/log_{$timestamp}_{$this->getIdent()}.txt";
    }

    abstract protected static function getIdent();

    abstract protected function runSpecificTask();
}
