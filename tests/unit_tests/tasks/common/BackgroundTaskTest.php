<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/tasks/common/BackgroundTask.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeTask extends BackgroundTask {
    public $setup_called = false;
    public $task_run = false;
    public $teardown_called = false;

    protected function setup() {
        $this->setup_called = true;
    }

    protected function teardown() {
        $this->teardown_called = true;
    }

    protected static function getIdent() {
        return "FakeTask";
    }

    protected function runSpecificTask() {
        $this->task_run = true;
    }
}

class FakeTaskWithoutSetupTeardown extends BackgroundTask {
    public $task_run = false;

    protected static function getIdent() {
        return "FakeTaskWithoutSetupTeardown";
    }

    protected function runSpecificTask() {
        $this->task_run = true;
    }
}

class FakeFailingTask extends BackgroundTask {
    public $task_run = false;

    protected static function getIdent() {
        return "FakeFailingTask";
    }

    protected function runSpecificTask() {
        $this->task_run = true;
        throw new Exception("Fake Error", 1);
    }
}

/**
 * @internal
 * @covers \BackgroundTask
 */
final class BackgroundTaskTest extends TestCase {
    public function testBackgroundTask(): void {
        global $data_path;
        $previous_data_path = $data_path;
        $data_path = '/fake/data/path/';
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = new Logger('SyncSolvTaskTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new FakeTask($date_utils);
        $job->setLogger($logger);
        $job->run();

        $this->assertSame('/fake/data/path/tasks/log_2020-03-13_19_30_00_FakeTask.txt', $job->generateLogPath());
        $this->assertSame(true, $job->setup_called);
        $this->assertSame(true, $job->task_run);
        $this->assertSame(true, $job->teardown_called);

        $data_path = $previous_data_path;
    }

    public function testTaskWithoutSetupTeardown(): void {
        global $data_path;
        $previous_data_path = $data_path;
        $data_path = '/fake/data/path/';
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = new Logger('SyncSolvTaskTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new FakeTaskWithoutSetupTeardown($date_utils);
        $job->setLogger($logger);
        $job->run();

        $this->assertSame('/fake/data/path/tasks/log_2020-03-13_19_30_00_FakeTaskWithoutSetupTeardown.txt', $job->generateLogPath());
        $this->assertSame(true, $job->task_run);

        $data_path = $previous_data_path;
    }

    public function testFailingTask(): void {
        global $data_path;
        $previous_data_path = $data_path;
        $data_path = '/fake/data/path/';
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = new Logger('SyncSolvTaskTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new FakeFailingTask($date_utils);
        $job->setLogger($logger);
        $job->run();

        $this->assertSame('/fake/data/path/tasks/log_2020-03-13_19_30_00_FakeFailingTask.txt', $job->generateLogPath());
        $this->assertSame(true, $job->task_run);

        $data_path = $previous_data_path;
    }
}
