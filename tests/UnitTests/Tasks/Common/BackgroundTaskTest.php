<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks\Common;

use Olz\Tasks\Common\BackgroundTask;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

class FakeBackgroundTask extends BackgroundTask {
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
        return "Fake\\FakeTask";
    }

    protected function runSpecificTask() {
        $this->task_run = true;
    }
}

class FakeTaskWithoutSetupTeardown extends BackgroundTask {
    public $task_run = false;

    protected static function getIdent() {
        return "Fake\\FakeTaskWithoutSetupTeardown";
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
        throw new \Exception("Fake Error", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Tasks\Common\BackgroundTask
 */
final class BackgroundTaskTest extends UnitTestCase {
    public function testBackgroundTask(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();

        $job = new FakeBackgroundTask($date_utils, $env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame(true, $job->setup_called);
        $this->assertSame(true, $job->task_run);
        $this->assertSame(true, $job->teardown_called);
    }

    public function testTaskWithoutSetupTeardown(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();

        $job = new FakeTaskWithoutSetupTeardown($date_utils, $env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame(true, $job->task_run);
    }

    public function testFailingTask(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();

        $job = new FakeFailingTask($date_utils, $env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame(true, $job->task_run);
    }
}
