<?php

require_once __DIR__.'/../../_/config/vendor/autoload.php';
require_once __DIR__.'/FakeLogHandler.php';

class FakeLogger extends Monolog\Logger {
    public $handler;

    public static function create($name = '') {
        $logger = new FakeLogger($name);
        $logger->handler = new FakeLogHandler();
        $logger->pushHandler($logger->handler);
        return $logger;
    }
}
