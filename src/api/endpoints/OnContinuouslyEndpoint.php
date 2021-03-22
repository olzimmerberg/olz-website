<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/StringField.php';

class OnContinuouslyEndpoint extends Endpoint {
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

        // TODO: Implement

        return [];
    }
}
