<?php

namespace Olz\Utils;

use Monolog\ErrorHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogsUtils {
    use WithUtilsTrait;

    private static array $activated_loggers_stack = [];

    public function getLogger(string $ident): Logger {
        $data_path = $this->envUtils()->getDataPath();
        $log_path = "{$data_path}logs/";
        if (!is_dir($log_path)) {
            mkdir($log_path, 0o777, true);
        }
        $logger = new Logger($ident);
        $logger->pushHandler(new RotatingFileHandler("{$log_path}merged.log", 366));
        $logger->pushProcessor(new OlzProcessor());
        return $logger;
    }

    public static function activateLogger(LoggerInterface|Logger $logger): void {
        $handler = new ErrorHandler($logger);
        $handler->registerErrorHandler();
        $handler->registerExceptionHandler();
        array_push(self::$activated_loggers_stack, $logger);
    }

    public static function deactivateLogger(LoggerInterface|Logger $logger): void {
        $expected_logger = array_pop(self::$activated_loggers_stack);
        if ($logger instanceof Logger && $expected_logger != $logger) {
            $expected_name = $expected_logger->getName();
            $actual_name = $logger->getName();
            $logger->error("Inconsistency deactivating handler: Expected {$expected_name}, but deactivating {$actual_name}");
        }
        restore_error_handler();
        restore_exception_handler();
    }

    public static function fromEnv(): self {
        return new self();
    }
}
