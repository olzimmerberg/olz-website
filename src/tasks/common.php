<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../config/vendor/autoload.php';

abstract class BackgroundTask {
    use Psr\Log\LoggerAwareTrait;

    private function setup() {
    }

    private function teardown() {
    }

    public function setDefaultFileLogger() {
        $log_path = $this->generate_log_path();
        if (!is_dir(dirname($log_path))) {
            mkdir(dirname($log_path), 0777, true);
        }
        $logger = new Logger($this->get_ident());
        $logger->pushHandler(new StreamHandler($log_path, Logger::INFO));
        $this->setLogger($logger);
    }

    public function run() {
        $this->logger->info("Setup task {$this->get_ident()}...");
        $this->setup();
        try {
            $this->logger->info("Running task {$this->get_ident()}...");
            $this->run_specific_task();
            $this->logger->info("Finished task {$this->get_ident()}.");
        } catch (Exception $exc) {
            $this->logger->error("Error running task {$this->get_ident()}.", [$exc]);
        } finally {
            $this->logger->info("Teardown task {$this->get_ident()}...");
            $this->teardown();
        }
    }

    protected function generate_log_path() {
        global $data_path;
        $timestamp = date('Y-m-d_H_i_s');
        return "{$data_path}tasks/log_{$timestamp}_{$this->get_ident()}.txt";
    }

    abstract protected static function get_ident();

    abstract protected function run_specific_task();
}
