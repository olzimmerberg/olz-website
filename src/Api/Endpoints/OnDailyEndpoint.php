<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Throttling;
use Olz\Fetchers\SolvFetcher;
use Olz\Tasks\CleanTempDirectoryTask;
use Olz\Tasks\SyncSolvTask;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class OnDailyEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        $clean_temp_directory_task = new CleanTempDirectoryTask(
            $this->dateUtils(),
            $this->envUtils()
        );
        $sync_solv_task = new SyncSolvTask(
            $this->entityManager(),
            new SolvFetcher(),
            $this->dateUtils(),
            $this->envUtils()
        );
        $this->setCleanTempDirectoryTask($clean_temp_directory_task);
        $this->setSyncSolvTask($sync_solv_task);
    }

    public function setCleanTempDirectoryTask($cleanTempDirectoryTask) {
        $this->cleanTempDirectoryTask = $cleanTempDirectoryTask;
    }

    public function setSyncSolvTask($syncSolvTask) {
        $this->syncSolvTask = $syncSolvTask;
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
        if ($this->envUtils()->hasUnlimitedCron()) {
            return false;
        }
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_daily = $throttling_repo->getLastOccurrenceOf('on_daily');
        if (!$last_daily) {
            return false;
        }
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $min_interval = \DateInterval::createFromDateString('+22 hours');
        $min_now = $last_daily->add($min_interval);
        return $now < $min_now;
    }

    protected function handle($input) {
        $expected_code = $this->envUtils()->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('on_daily', $this->dateUtils()->getIsoNow());

        $this->cleanTempDirectoryTask->run();
        $this->syncSolvTask->run();
        $this->telegramUtils()->sendConfiguration();

        return [];
    }
}
