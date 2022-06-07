<?php

declare(strict_types=1);

use Monolog\Logger;
use Olz\Utils\LogsUtils;

require_once __DIR__.'/../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \LogsUtils
 */
final class LogsUtilsTest extends UnitTestCase {
    public function testLogsUtilsGetLogger(): void {
        $env_utils = new FakeEnvUtils();
        $data_path = $env_utils->getDataPath();
        $logs_path = "{$data_path}logs/";
        $this->assertSame(false, is_dir($logs_path));
        $logs_utils = new LogsUtils();
        $logs_utils->setEnvUtils($env_utils);

        $logger = $logs_utils->getLogger('test');
        $logger->debug('just for test');

        $this->assertSame('test', $logger->getName());
        $this->assertSame(true, is_dir($data_path));
        $this->assertSame(true, is_dir($logs_path));
        $this->assertMatchesRegularExpression(
            '/^merged\\-[0-9]{4}\\-[0-9]{2}\\-[0-9]{2}\\.log$/',
            scandir($logs_path)[2]
        );
    }

    public function testLogsUtilsActivateDeactivateLogger(): void {
        // just to be safe...
        restore_exception_handler();
        restore_exception_handler();

        $logger1 = new Logger('logger1');
        $logger2 = new Logger('logger2');

        $null_handler = set_exception_handler(null);
        $this->assertSame(null, $null_handler);
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
        $this->assertSame(null, $null_handler2);
        restore_exception_handler();
    }

    public function testLogsUtilsActivateDeactivateLoggerInconsistency(): void {
        $logger1 = FakeLogger::create('logger1');
        $logger2 = FakeLogger::create('logger2');

        LogsUtils::activateLogger($logger1);
        LogsUtils::activateLogger($logger2);
        LogsUtils::deactivateLogger($logger1);

        $this->assertSame([
            "ERROR Inconsistency deactivating handler: Expected logger2, but deactivating logger1",
        ], $logger1->handler->getPrettyRecords());
        $this->assertSame([], $logger2->handler->getPrettyRecords());
        $logger1->handler->records = [];

        LogsUtils::activateLogger($logger2);
        LogsUtils::deactivateLogger($logger2);
        LogsUtils::deactivateLogger($logger1);

        $this->assertSame([], $logger1->handler->getPrettyRecords());
        $this->assertSame([], $logger2->handler->getPrettyRecords());
    }
}
