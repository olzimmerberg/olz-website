<?php

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../model/RunningTask.php';
require_once __DIR__.'/../../fetchers/ContinueAsyncTaskFetcher.php';
require_once __DIR__.'/../../utils/env/LogsUtils.php';
require_once __DIR__.'/../../utils/GeneralUtils.php';

abstract class AsyncTask {
    use Psr\Log\LoggerAwareTrait;

    public function __construct($entityManager, $dateUtils, $envUtils) {
        $this->continueAsyncTaskFetcher = new ContinueAsyncTaskFetcher();
        $this->dateUtils = $dateUtils;
        $this->envUtils = $envUtils;
        $logger = $this->envUtils->getLogsUtils()->getLogger("Task:{$this->getIdent()}");
        $this->setLogger($logger);
        $this->entityManager = $entityManager;
        $this->generalUtils = new GeneralUtils();
    }

    protected function getTaskId() {
        return $this->runningTask->getId();
    }

    protected function getState() {
        $state_json = $this->runningTask->getState();
        return json_decode($state_json, true);
    }

    protected function setState($new_state) {
        $new_state_json = json_encode($new_state);
        $this->runningTask->setState($new_state_json);
        $this->entityManager->flush();
    }

    protected function setup() {
    }

    protected function teardown() {
    }

    public function start() {
        $now_datetime = new DateTime($this->dateUtils->getIsoNow());
        $task_id = $this->getRandomTaskId();

        $running_task = new RunningTask();
        $running_task->setId($task_id);
        $running_task->setTaskClass(get_class($this));
        $running_task->setIsCurrentlyExecuting(true);
        $running_task->setState(json_encode([]));
        $running_task->setCreatedAt($now_datetime);

        $this->entityManager->persist($running_task);
        $this->entityManager->flush();

        $this->runningTask = $running_task;

        $this->logger->info("Setup task {$this->getIdent()}...");
        $this->setup();

        $this->continue();

        return $task_id;
    }

    public function process($running_task) {
        $this->runningTask = $running_task;

        LogsUtils::activateLogger($this->logger);

        if ($this->runningTask->getIsCurrentlyExecuting()) {
            $this->logger->error("Task {$this->getIdent()} is already executing.", []);
            return;
        }
        $this->runningTask->setIsCurrentlyExecuting(true);
        $this->entityManager->flush();

        try {
            $this->logger->info("Process task {$this->getIdent()}...");
            $this->processSpecificTask();
        } catch (Exception $exc) {
            $this->logger->error("Error processing task {$this->getIdent()}.", [$exc]);
        }

        LogsUtils::deactivateLogger($this->logger);
    }

    public function continue() {
        $this->logger->info("Continue task {$this->getIdent()}...");
        $this->runningTask->setIsCurrentlyExecuting(false);
        $this->entityManager->flush();

        $url = $this->getContinuationUrl();
        $result = $this->continueAsyncTaskFetcher->continueAsyncTask($url);
        $this->logger->info("Continue task result: '{$result}'...");
    }

    public function finish() {
        $this->logger->info("Finished task {$this->getIdent()}...");

        $this->logger->info("Teardown task {$this->getIdent()}...");
        $this->teardown($this->runningTask);

        $this->entityManager->remove($this->runningTask);
        $this->entityManager->flush();
    }

    abstract protected static function getIdent();

    abstract protected function getContinuationUrl();

    abstract protected function processSpecificTask();

    protected function getRandomTaskId() {
        return $this->generalUtils->base64EncodeUrl(openssl_random_pseudo_bytes(32));
    }
}
