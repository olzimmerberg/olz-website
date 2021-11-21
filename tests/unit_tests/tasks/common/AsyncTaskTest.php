<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/tasks/common/AsyncTask.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeAsyncTask extends AsyncTask {
    public $got_state;
    public $got_task_id;

    public $setup_called = false;
    public $task_run = false;
    public $teardown_called = false;
    public $should_finish = false;

    protected function setup() {
        $this->setup_called = true;
    }

    protected function teardown() {
        $this->teardown_called = true;
    }

    protected static function getIdent() {
        return "FakeTask";
    }

    protected function getContinuationUrl() {
        return 'fake-continuation-url';
    }

    protected function processSpecificTask() {
        $this->task_run = true;
        $this->got_state = $this->getState();
        $this->setState(['step' => 2]);
        $this->got_task_id = $this->getTaskId();
        if ($this->should_finish) {
            return $this->finish();
        }
        return $this->continue();
    }

    protected function getRandomTaskId() {
        return 'random-task-id';
    }
}

class FakeAsyncTaskWithoutSetupTeardown extends AsyncTask {
    public $task_run = false;
    public $should_finish = false;

    protected static function getIdent() {
        return "FakeAsyncTaskWithoutSetupTeardown";
    }

    protected function getContinuationUrl() {
        return 'fake-continuation-url';
    }

    protected function processSpecificTask() {
        $this->task_run = true;
        if ($this->should_finish) {
            return $this->finish();
        }
        return $this->continue();
    }
}

class FakeFailingAsyncTask extends AsyncTask {
    public $task_run = false;

    protected static function getIdent() {
        return "FakeFailingAsyncTask";
    }

    protected function getContinuationUrl() {
        return 'fake-continuation-url';
    }

    protected function processSpecificTask() {
        $this->task_run = true;
        throw new Exception("Fake Error", 1);
    }

    protected function getRandomTaskId() {
        return 'random-task-id';
    }
}

class FakeContinueAsyncTaskFetcher {
    public function continueAsyncTask($url) {
        return 'continued';
    }
}

/**
 * @internal
 * @covers \AsyncTask
 */
final class AsyncTaskTest extends UnitTestCase {
    public function testAsyncTaskStart(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();

        $job = new FakeAsyncTask($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->start();

        $this->assertSame(true, $job->setup_called);
        $this->assertSame(false, $job->task_run);
        $this->assertSame(false, $job->teardown_called);
        $this->assertSame([
            "INFO Setup task FakeTask...",
            "INFO Continue task FakeTask...",
            "INFO Continue task result: 'continued'...",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(1, count($entity_manager->persisted));
        $running_task = $entity_manager->persisted[0];
        $this->assertSame('random-task-id', $running_task->getId());
        $this->assertSame('FakeAsyncTask', $running_task->getTaskClass());
        $this->assertSame(false, $running_task->getIsCurrentlyExecuting());
        $this->assertSame('[]', $running_task->getState());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $running_task->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function testAsyncTaskProcess(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $running_task = new RunningTask();
        $running_task->setIsCurrentlyExecuting(false);
        $running_task->setId('fake-running-task-id');
        $running_task->setState(json_encode(['step' => 1]));

        $job = new FakeAsyncTask($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->process($running_task);

        $this->assertSame(['step' => 1], $job->got_state);
        $this->assertSame('fake-running-task-id', $job->got_task_id);
        $this->assertSame(json_encode(['step' => 2]), $running_task->getState());

        $this->assertSame(false, $job->setup_called);
        $this->assertSame(true, $job->task_run);
        $this->assertSame(false, $job->teardown_called);
        $this->assertSame([
            "INFO Process task FakeTask...",
            "INFO Continue task FakeTask...",
            "INFO Continue task result: 'continued'...",
        ], $logger->handler->getPrettyRecords());
    }

    public function testAsyncTaskProcessCurrentlyExecuting(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $running_task = new RunningTask();
        $running_task->setIsCurrentlyExecuting(true);

        $job = new FakeAsyncTask($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->process($running_task);

        $this->assertSame(false, $job->setup_called);
        $this->assertSame(false, $job->task_run);
        $this->assertSame(false, $job->teardown_called);
        $this->assertSame([
            "ERROR Task FakeTask is already executing.",
        ], $logger->handler->getPrettyRecords());
    }

    public function testAsyncTaskFinish(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $running_task = new RunningTask();

        $job = new FakeAsyncTask($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->should_finish = true;
        $job->process($running_task);

        $this->assertSame(false, $job->setup_called);
        $this->assertSame(true, $job->task_run);
        $this->assertSame(true, $job->teardown_called);
        $this->assertSame([
            "INFO Process task FakeTask...",
            "INFO Finished task FakeTask...",
            "INFO Teardown task FakeTask...",
        ], $logger->handler->getPrettyRecords());
    }

    public function testStartTaskWithoutSetup(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();

        $job = new FakeAsyncTaskWithoutSetupTeardown($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->start();

        $this->assertSame(false, $job->task_run);
        $this->assertSame([
            "INFO Setup task FakeAsyncTaskWithoutSetupTeardown...",
            "INFO Continue task FakeAsyncTaskWithoutSetupTeardown...",
            "INFO Continue task result: 'continued'...",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(1, count($entity_manager->persisted));
        $running_task = $entity_manager->persisted[0];
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-_]{43}$/',
            $running_task->getId()
        );
        $this->assertSame(
            'FakeAsyncTaskWithoutSetupTeardown',
            $running_task->getTaskClass()
        );
        $this->assertSame(false, $running_task->getIsCurrentlyExecuting());
        $this->assertSame('[]', $running_task->getState());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $running_task->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function testFinishTaskWithoutTeardown(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $running_task = new RunningTask();
        $running_task->setIsCurrentlyExecuting(false);

        $job = new FakeAsyncTaskWithoutSetupTeardown($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->should_finish = true;
        $job->process($running_task);

        $this->assertSame(true, $job->task_run);
        $this->assertSame([
            "INFO Process task FakeAsyncTaskWithoutSetupTeardown...",
            "INFO Finished task FakeAsyncTaskWithoutSetupTeardown...",
            "INFO Teardown task FakeAsyncTaskWithoutSetupTeardown...",
        ], $logger->handler->getPrettyRecords());
    }

    public function testFailingTask(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $running_task = new RunningTask();
        $running_task->setIsCurrentlyExecuting(false);

        $job = new FakeFailingAsyncTask($entity_manager, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->continueAsyncTaskFetcher = new FakeContinueAsyncTaskFetcher();
        $job->process($running_task);

        $this->assertSame(true, $job->task_run);
        $this->assertSame([
            "INFO Process task FakeFailingAsyncTask...",
            "ERROR Error processing task FakeFailingAsyncTask.",
        ], $logger->handler->getPrettyRecords());
    }
}
