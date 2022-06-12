<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetLogsEndpoint extends OlzEndpoint {
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
        $filenames = $this->scandir($logs_path, SCANDIR_SORT_DESCENDING);
        foreach ($filenames as $filename) {
            if (preg_match('/^merged-.*\.log$/', $filename)) {
                if ($merged_log_index == $input['index']) {
                    return [
                        'content' => $this->readFile("{$logs_path}{$filename}"),
                    ];
                }
                $merged_log_index++;
            }
        }
        return [
            'content' => null,
        ];
    }

    protected function scandir($path, $sorting) {
        return scandir($path, $sorting);
    }

    protected function readFile($path) {
        return file_get_contents($path);
    }
}
