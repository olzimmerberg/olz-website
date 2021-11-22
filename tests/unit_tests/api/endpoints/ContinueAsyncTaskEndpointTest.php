<?php

declare(strict_types=1);

use Monolog\Logger;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../src/api/endpoints/ContinueAsyncTaskEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/RunningTask.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../../fake/FakeTask.php';
require_once __DIR__.'/../../../fake/FakeTelegramUtils.php';
require_once __DIR__.'/../../../fake/FakeThrottlingRepository.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeRunningTaskRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 'currently-executing-task-id']) {
            $running_task = new RunningTask();
            $running_task->setIsCurrentlyExecuting(true);
            return $running_task;
        }
        if ($where === ['id' => 'task-id-with-invalid-task-class']) {
            $running_task = new RunningTask();
            $running_task->setIsCurrentlyExecuting(false);
            $running_task->setTaskClass('invalid-task-class-name');
            return $running_task;
        }
        if ($where === ['id' => 'valid-task-id']) {
            $running_task = new RunningTask();
            $running_task->setIsCurrentlyExecuting(false);
            $running_task->setTaskClass('LogForAnHourAsyncTask');
            return $running_task;
        }
        return null;
    }
}

class ContinueAsyncTaskEndpointTestLogForAnHourAsyncTask {
    public $process_called_with = [];

    public function process($running_task) {
        $this->process_called_with[] = $running_task;
    }
}

/**
 * @internal
 * @covers \ContinueAsyncTaskEndpoint
 */
final class ContinueAsyncTaskEndpointTest extends UnitTestCase {
    public function testContinueAsyncTaskEndpointIdent(): void {
        $endpoint = new ContinueAsyncTaskEndpoint();
        $this->assertSame('ContinueAsyncTaskEndpoint', $endpoint->getIdent());
    }

    public function testContinueAsyncTaskEndpointParseInput(): void {
        global $_GET;
        $_GET = ['taskId' => 'some-task-id'];
        $endpoint = new ContinueAsyncTaskEndpoint();
        $parsed_input = $endpoint->parseInput();
        $this->assertSame([
            'taskId' => 'some-task-id',
        ], $parsed_input);
    }

    public function testContinueAsyncTaskEndpointInexistentTaskId(): void {
        $entity_manager = new FakeEntityManager();
        $running_task_repo = new FakeRunningTaskRepository();
        $entity_manager->repositories['RunningTask'] = $running_task_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $logger = new Logger('ContinueAsyncTaskEndpointTest');
        $endpoint = new ContinueAsyncTaskEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call([
                'taskId' => 'inexistent-task-id',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode());
        }
    }

    public function testContinueAsyncTaskEndpointCurrentlyExecuting(): void {
        $entity_manager = new FakeEntityManager();
        $running_task_repo = new FakeRunningTaskRepository();
        $entity_manager->repositories['RunningTask'] = $running_task_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $logger = new Logger('ContinueAsyncTaskEndpointTest');
        $endpoint = new ContinueAsyncTaskEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call([
                'taskId' => 'currently-executing-task-id',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode());
        }
    }

    public function testContinueAsyncTaskEndpointNoSuchTaskClass(): void {
        $entity_manager = new FakeEntityManager();
        $running_task_repo = new FakeRunningTaskRepository();
        $entity_manager->repositories['RunningTask'] = $running_task_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $logger = new Logger('ContinueAsyncTaskEndpointTest');
        $endpoint = new ContinueAsyncTaskEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call([
                'taskId' => 'task-id-with-invalid-task-class',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode());
        }
    }

    public function testContinueAsyncTaskEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $running_task_repo = new FakeRunningTaskRepository();
        $entity_manager->repositories['RunningTask'] = $running_task_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $fake_task = new ContinueAsyncTaskEndpointTestLogForAnHourAsyncTask();
        $logger = new Logger('ContinueAsyncTaskEndpointTest');
        $endpoint = new ContinueAsyncTaskEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogForAnHourAsyncTask($fake_task);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'taskId' => 'valid-task-id',
        ]);

        $this->assertSame([], $result);
        $this->assertSame([
            'LogForAnHourAsyncTask',
        ], array_map(
            function ($running_task) {
                return $running_task->getTaskClass();
            },
            $fake_task->process_called_with
        ));
    }
}
