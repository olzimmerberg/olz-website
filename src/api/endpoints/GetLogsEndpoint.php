<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../OlzEndpoint.php';

class GetLogsEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG;
        require_once __DIR__.'/../../config/server.php';
        $this->setEnvUtils($_CONFIG);
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public static function getIdent() {
        return 'GetLogsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'content' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'index' => new FieldTypes\IntegerField([
                'default_value' => 0,
                'min_value' => 0,
            ]),
        ]]);
    }

    protected function handle($input) {
        if ($this->session->get('auth') != 'all') {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $username = $this->session->get('user');
        $this->logger->info("Logs access by {$username}.");

        $data_path = $this->envUtils->getDataPath();
        $logs_path = "{$data_path}logs/";

        $merged_log_index = 0;
        foreach (scandir($logs_path, SCANDIR_SORT_DESCENDING) as $filename) {
            if (preg_match('/^merged-.*\.log$/', $filename)) {
                if ($merged_log_index == $input['index']) {
                    return [
                        'content' => file_get_contents("{$logs_path}{$filename}"),
                    ];
                }
                $merged_log_index++;
            }
        }
        return [
            'content' => null,
        ];
    }
}
