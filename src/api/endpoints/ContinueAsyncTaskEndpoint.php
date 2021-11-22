<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../OlzEndpoint.php';
require_once __DIR__.'/../../model/RunningTask.php';

class ContinueAsyncTaskEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../config/server.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../tasks/LogForAnHourAsyncTask.php';
        $this->setEntityManager($entityManager);
        $this->setDateUtils($_DATE);
        $this->setEnvUtils($_CONFIG);
        $log_for_an_hour_async_task = new LogForAnHourAsyncTask(
            $this->entityManager,
            $this->dateUtils,
            $this->envUtils
        );
        $this->setLogForAnHourAsyncTask($log_for_an_hour_async_task);
    }

    public function setLogForAnHourAsyncTask($logForAnHourAsyncTask) {
        $this->logForAnHourAsyncTask = $logForAnHourAsyncTask;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public static function getIdent() {
        return 'ContinueAsyncTaskEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'taskId' => new FieldTypes\StringField([]),
        ]]);
    }

    public function parseInput() {
        global $_GET;
        $input = [
            'taskId' => $_GET['taskId'],
        ];
        return $input;
    }

    protected function handle($input) {
        $task_id = $input['taskId'];

        $running_task_repo = $this->entityManager->getRepository(RunningTask::class);
        $running_task = $running_task_repo->findOneBy(['id' => $task_id]);
        if (!$running_task) {
            throw new HttpError(400, "No such running task");
        }
        if ($running_task->getIsCurrentlyExecuting()) {
            throw new HttpError(400, "Task is currently executing");
        }

        $task_class = $running_task->getTaskClass();
        if ($task_class === 'LogForAnHourAsyncTask') {
            $this->logForAnHourAsyncTask->process($running_task);
            return [];
        }
        throw new HttpError(400, "No such task class: {$task_class}");
    }
}
