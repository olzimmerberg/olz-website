<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/Throttling.php';

class OnContinuouslyEndpoint extends Endpoint {
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
        return 'OnContinuouslyEndpoint';
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

    protected function handle($input) {
        $expected_code = $this->envUtils->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        if ($this->shouldSendDailyMailNow()) {
            $throttling_repo = $this->entityManager->getRepository(Throttling::class);
            $throttling_repo->recordOccurrenceOf('daily_notifications', $this->dateUtils->getIsoNow());

            $this->logger->info("TODO: Send daily mail now.");
        }

        // TODO: Implement

        return [];
    }

    public function shouldSendDailyMailNow(): bool {
        $daily_notifications_time = '16:27:00';
        $throttling_repo = $this->entityManager->getRepository(Throttling::class);
        $last_daily_notifications = $throttling_repo->getLastOccurrenceOf('daily_notifications');
        $is_too_soon = false;
        if ($last_daily_notifications) {
            $now = new DateTime($this->dateUtils->getIsoNow());
            // Consider daylight saving change date => not 23 hours!
            $min_interval = DateInterval::createFromDateString('+22 hours');
            $min_now = $last_daily_notifications->add($min_interval);
            $is_too_soon = $now < $min_now;
        }
        $is_right_time_of_day = $this->dateUtils->getCurrentDateInFormat('H:i:s') >= $daily_notifications_time;
        return !$is_too_soon && $is_right_time_of_day;
    }
}
