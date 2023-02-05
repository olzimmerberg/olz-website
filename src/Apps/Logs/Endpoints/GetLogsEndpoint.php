<?php

namespace Olz\Apps\Logs\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Apps\Logs\Utils\OlzLogsChannel;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetLogsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetLogsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'content' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\StringField(['allow_empty' => true]),
            ]),
            'pagination' => new FieldTypes\ObjectField(['field_structure' => [
                'previous' => new FieldTypes\StringField(['allow_null' => true]),
                'next' => new FieldTypes\StringField(['allow_null' => true]),
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'query' => new FieldTypes\ObjectField([
                'export_as' => 'OlzLogsQuery',
                'field_structure' => [
                    'targetDate' => new FieldTypes\DateTimeField(['allow_null' => true]),
                    'firstDate' => new FieldTypes\DateTimeField(['allow_null' => true]),
                    'lastDate' => new FieldTypes\DateTimeField(['allow_null' => true]),
                    'minLogLevel' => new FieldTypes\EnumField([
                        'export_as' => 'OlzLogLevel',
                        'allow_null' => true,
                        'allowed_values' => ['debug', 'info', 'notice', 'warning', 'error'],
                    ]),
                    'textSearch' => new FieldTypes\StringField(['allow_null' => true]),
                    'pageToken' => new FieldTypes\StringField(['allow_null' => true]),
                ],
            ]),
        ]]);
    }

    protected function handle($input) {
        if ($this->session()->get('auth') != 'all') {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $username = $this->session()->get('user');
        $this->log()->info("Logs access by {$username}.");

        $channel = new OlzLogsChannel();
        $channel->setEnvUtils($this->envUtils());
        $channel->setLog($this->log());
        $result = $channel->readLogs($input['query']);

        return [
            'content' => $result->lines,
            'pagination' => [
                // TODO: Encrypt!
                'previous' => $result->previous ? json_encode($result->previous) : null,
                'next' => $result->next ? json_encode($result->next) : null,
            ],
        ];
    }
}
