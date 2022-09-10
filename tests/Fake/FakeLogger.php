<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeLogger extends \Monolog\Logger {
    public $handler;

    public static function create($name = '') {
        $logger = new FakeLogger($name);
        $logger->handler = new FakeLogHandler();
        $logger->pushHandler($logger->handler);
        return $logger;
    }
}
