<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../OlzEndpoint.php';
require_once __DIR__.'/../../model/Throttling.php';

class OnDailyEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../config/server.php';
        require_once __DIR__.'/../../fetchers/SolvFetcher.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../tasks/CleanTempDirectoryTask.php';
        require_once __DIR__.'/../../tasks/SyncSolvTask.php';
        require_once __DIR__.'/../../utils/notify/TelegramUtils.php';
        $telegram_utils = getTelegramUtilsFromEnv();
        $date_utils = $_DATE;
        $clean_temp_directory_task = new CleanTempDirectoryTask($date_utils, $_CONFIG);
        $sync_solv_task = new SyncSolvTask($entityManager, new SolvFetcher(), $date_utils, $_CONFIG);
        $this->setCleanTempDirectoryTask($clean_temp_directory_task);
        $this->setSyncSolvTask($sync_solv_task);
        $this->setEntityManager($entityManager);
        $this->setDateUtils($date_utils);
        $this->setEnvUtils($_CONFIG);
        $this->setTelegramUtils($telegram_utils);
    }

    public function setCleanTempDirectoryTask($cleanTempDirectoryTask) {
        $this->cleanTempDirectoryTask = $cleanTempDirectoryTask;
    }

    public function setSyncSolvTask($syncSolvTask) {
        $this->syncSolvTask = $syncSolvTask;
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

    public function setTelegramUtils($telegramUtils) {
        $this->telegramUtils = $telegramUtils;
    }

    public static function getIdent() {
        return 'OnDailyEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'authenticityCode' => new FieldTypes\StringField([]),
        ]]);
    }

    public function parseInput() {
        global $_GET;
        $input = [
            'authenticityCode' => $_GET['authenticityCode'],
        ];
        return $input;
    }

    public function shouldFailThrottling() {
        if ($this->envUtils->hasUnlimitedCron()) {
            return false;
        }
        $throttling_repo = $this->entityManager->getRepository(Throttling::class);
        $last_daily = $throttling_repo->getLastOccurrenceOf('on_daily');
        if (!$last_daily) {
            return false;
        }
        $now = new DateTime($this->dateUtils->getIsoNow());
        $min_interval = DateInterval::createFromDateString('+22 hours');
        $min_now = $last_daily->add($min_interval);
        return $now < $min_now;
    }

    protected function handle($input) {
        $expected_code = $this->envUtils->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $throttling_repo = $this->entityManager->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('on_daily', $this->dateUtils->getIsoNow());

        $this->cleanTempDirectoryTask->run();
        $this->syncSolvTask->run();
        $this->telegramUtils->sendConfiguration();

        // TODO: Remove again
        require_once __DIR__.'/../../tasks/LogForAnHourAsyncTask.php';
        $log_for_an_hour_async_task = new LogForAnHourAsyncTask(
            $this->entityManager,
            $this->dateUtils,
            $this->envUtils
        );
        $log_for_an_hour_async_task->start();

        return [];
    }
}
