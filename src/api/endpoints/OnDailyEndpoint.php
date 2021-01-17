<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/StringField.php';

class OnDailyEndpoint extends Endpoint {
    public function setSyncSolvTask($syncSolvTask) {
        $this->syncSolvTask = $syncSolvTask;
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

    protected function handle($input) {
        $expected_code = $this->serverConfig->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->syncSolvTask->run();

        return [];
    }
}
