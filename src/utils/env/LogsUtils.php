<?php

use Monolog\ErrorHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/EnvUtils.php';

class LogsUtils {
    private static $activated_loggers_stack = [];

    private $envUtils;

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getLogger($ident) {
        $data_path = $this->envUtils->getDataPath();
        $log_path = "{$data_path}logs/";
        if (!is_dir($log_path)) {
            mkdir($log_path, 0777, true);
        }
        $logger = new Logger($ident);
        $logger->pushHandler(new RotatingFileHandler("{$log_path}merged.log", 366));
        return $logger;
    }

    public static function activateLogger($logger) {
        $handler = new ErrorHandler($logger);
        $handler->registerErrorHandler();
        $handler->registerExceptionHandler();
        array_push(self::$activated_loggers_stack, $logger);
    }

    public static function deactivateLogger($logger) {
        $expected_logger = array_pop(self::$activated_loggers_stack);
        if ($expected_logger != $logger) {
            $expected_name = $expected_logger->getName();
            $actual_name = $logger->getName();
            $logger->error("Inconsistency deactivating handler: Expected {$expected_name}, but deactivating {$actual_name}");
        }
        restore_error_handler();
        restore_exception_handler();
    }

    public static function fromEnv() {
        $env_utils = EnvUtils::fromEnv();
        $logs_utils = new self();
        $logs_utils->setEnvUtils($env_utils);
        return $logs_utils;
    }
}
