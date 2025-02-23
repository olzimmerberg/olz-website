<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Monolog\Logger;
use Olz\Tests\Fake\FakeLogHandler;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\LogsUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Utils\LogsUtils
 */
final class LogsUtilsTest extends UnitTestCase {
    public function testLogsUtilsGetLogger(): void {
        $private_path = WithUtilsCache::get('envUtils')->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $this->assertFalse(is_dir($logs_path));
        $logs_utils = new LogsUtils();

        $logger = $logs_utils->getLogger('test');
        $logger->debug('just for test');

        $this->assertSame('test', $logger->getName());
        $this->assertTrue(is_dir($private_path));
        $this->assertTrue(is_dir($logs_path));
        $this->assertMatchesRegularExpression(
            '/^merged\-[0-9]{4}\-[0-9]{2}\-[0-9]{2}\.log$/',
            (scandir($logs_path) ?: [])[2]
        );
    }

    public function testLogsUtilsActivateDeactivateLogger(): void {
        // just to be safe...
        restore_exception_handler();
        restore_exception_handler();

        $logger1 = new Logger('logger1');
        $logger2 = new Logger('logger2');

        $null_handler = set_exception_handler(null);
        $this->assertNull($null_handler);
        restore_exception_handler();

        LogsUtils::activateLogger($logger1);

        $logger1_handler1 = set_exception_handler(null);
        $this->assertNotSame(null, $logger1_handler1);
        restore_exception_handler();

        LogsUtils::activateLogger($logger2);

        $logger2_handler = set_exception_handler(null);
        $this->assertNotSame(null, $logger2_handler);
        $this->assertNotSame($logger1_handler1, $logger2_handler);
        restore_exception_handler();

        LogsUtils::deactivateLogger($logger2);

        $logger1_handler2 = set_exception_handler(null);
        $this->assertSame($logger1_handler1, $logger1_handler2);
        restore_exception_handler();

        LogsUtils::deactivateLogger($logger1);

        $null_handler2 = set_exception_handler(null);
        $this->assertNull($null_handler2);
        restore_exception_handler();
    }

    public function testLogsUtilsActivateDeactivateLoggerInconsistency(): void {
        $logger1 = new Logger('logger1');
        $handler1 = new FakeLogHandler();
        $logger1->pushHandler($handler1);
        $logger2 = new Logger('logger2');
        $handler2 = new FakeLogHandler();
        $logger2->pushHandler($handler2);

        LogsUtils::activateLogger($logger1);
        LogsUtils::activateLogger($logger2);
        LogsUtils::deactivateLogger($logger1);

        $this->assertSame([
            "ERROR Inconsistency deactivating handler: Expected logger2, but deactivating logger1",
        ], $handler1->getPrettyRecords());
        $this->assertSame([], $handler2->getPrettyRecords());
        $handler1->records = [];

        LogsUtils::activateLogger($logger2);
        LogsUtils::deactivateLogger($logger2);
        LogsUtils::deactivateLogger($logger1);

        $this->assertSame([], $handler1->getPrettyRecords());
        $this->assertSame([], $handler2->getPrettyRecords());
    }
}
