<?php

namespace Olz\Apps\Logs\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\LineLocation;
use Olz\Apps\Logs\Utils\LogsDefinitions;
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
                    'channel' => new FieldTypes\StringField(['allow_null' => false]),
                    'targetDate' => new FieldTypes\DateTimeField(['allow_null' => true]),
                    'firstDate' => new FieldTypes\DateTimeField(['allow_null' => true]),
                    'lastDate' => new FieldTypes\DateTimeField(['allow_null' => true]),
                    'minLogLevel' => new FieldTypes\EnumField([
                        'export_as' => 'OlzLogLevel',
                        'allow_null' => true,
                        'allowed_values' => BaseLogsChannel::LOG_LEVELS,
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

        $channel = null;
        foreach (LogsDefinitions::getLogsChannels() as $current_channel) {
            if ($current_channel::getId() === $input['query']['channel']) {
                $channel = new $current_channel();
            }
        }
        $channel->setEnvUtils($this->envUtils());
        $channel->setLog($this->log());
        $result = $channel->readLogs($input['query']);

        return [
            'content' => $result->lines,
            'pagination' => [
                // TODO: Encrypt!
                'previous' => $this->serializeLineLocation($result->previous),
                'next' => $this->serializeLineLocation($result->next),
            ],
        ];
    }

    protected function serializeLineLocation(
        LineLocation|null $line_location,
    ): string|null {
        if (!$line_location) {
            return null;
        }
        return json_encode([
            'logFile' => $line_location->logFile->getPath(),
            'lineNumber' => $line_location->lineNumber,
        ]);
    }
}
