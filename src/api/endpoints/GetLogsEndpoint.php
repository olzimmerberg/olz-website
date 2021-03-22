<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../common/HttpError.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class GetLogsEndpoint extends Endpoint {
    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public static function getIdent() {
        return 'GetLogsEndpoint';
    }

    public function getResponseFields() {
        return [
            new StringField('content', ['allow_null' => true]),
        ];
    }

    public function getRequestFields() {
        return [
            new IntegerField('index', [
                'default_value' => 0,
                'min_value' => 0,
            ]),
        ];
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
