<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../common/HttpError.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class GetLogsEndpoint extends Endpoint {
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
        global $data_path;

        if ($this->session->get('auth') != 'all') {
            throw new HttpError(403, "Kein Zugriff!");
        }

        require_once __DIR__.'/../../config/paths.php';

        $this->logger->info('Logs access!');

        $logs_path = $data_path.'logs/';

        $merged_log_index = 0;
        foreach (scandir($logs_path, SCANDIR_SORT_DESCENDING) as $filename) {
            if (preg_match('/^merged-.*\.log$/', $filename)) {
                if ($merged_log_index == $input['index']) {
                    return [
                        'content' => file_get_contents("{$logs_path}{$filename}"),
                    ];
                }
            }
        }
        return [
            'content' => null,
        ];
    }
}
