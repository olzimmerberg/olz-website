<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/Throttling.php';

class OnDailyEndpoint extends Endpoint {
    public function setSyncSolvTask($syncSolvTask) {
        $this->syncSolvTask = $syncSolvTask;
    }

    public function setSendDailyNotificationsTask($sendDailyNotificationsTask) {
        $this->sendDailyNotificationsTask = $sendDailyNotificationsTask;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setServerConfig($serverConfig) {
        $this->serverConfig = $serverConfig;
    }

    public static function getIdent() {
        return 'OnDailyEndpoint';
    }

    public function getResponseFields() {
        return [];
    }

    public function getRequestFields() {
        return [
            new StringField('authenticityCode', []),
        ];
    }

    public function parseInput() {
        global $_GET;
        $input = [
            'authenticityCode' => $_GET['authenticityCode'],
        ];
        return $input;
    }

    public function shouldFailThrottling() {
        if ($this->serverConfig->hasUnlimitedCron()) {
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
        $expected_code = $this->serverConfig->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $throttling_repo = $this->entityManager->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('on_daily', $this->dateUtils->getIsoNow());

        $this->syncSolvTask->run();

        $this->sendDailyNotificationsTask->run();

        return [];
    }
}
