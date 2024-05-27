<?php

namespace Olz\Apps\Logs\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\GzLogFile;
use Olz\Apps\Logs\Utils\LineLocation;
use Olz\Apps\Logs\Utils\LogsDefinitions;
use Olz\Apps\Logs\Utils\PlainLogFile;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetLogsEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'GetLogsEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
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

    public function getRequestField(): FieldTypes\Field {
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
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Logs access by {$user->getUsername()}.");

        $channel = null;
        foreach (LogsDefinitions::getLogsChannels() as $current_channel) {
            if ($current_channel::getId() === $input['query']['channel']) {
                $channel = new $current_channel();
            }
        }
        $channel->setEnvUtils($this->envUtils());
        $channel->setLog($this->log());

        $query = $input['query'];
        $page_token = $query['pageToken'] ?? null;
        $date_time = $query['targetDate'] ?? null;
        $result = null;
        if ($page_token) {
            $deserialized = $this->deserializePageToken($page_token);
            $result = $channel->continueReading(
                $deserialized['lineLocation'],
                $deserialized['mode'],
                $query,
            );
        } elseif ($date_time) {
            $result = $channel->readAroundDateTime(
                new \DateTime($date_time),
                $query,
            );
        } else {
            throw new HttpError(400, "Need to provide targetDate or pageToken");
        }

        return [
            'content' => $result->lines,
            'pagination' => [
                // TODO: Encrypt!
                'previous' => $this->serializePageToken($result->previous, 'previous'),
                'next' => $this->serializePageToken($result->next, 'next'),
            ],
        ];
    }

    protected function serializePageToken(
        ?LineLocation $line_location,
        ?string $mode,
    ): ?string {
        if (!$line_location) {
            return null;
        }
        return json_encode([
            'logFile' => $line_location->logFile->serialize(),
            'lineNumber' => $line_location->lineNumber,
            'mode' => $mode,
        ]);
    }

    protected function deserializePageToken(
        string $serialized,
    ) {
        $data = json_decode($serialized, true);
        $log_file = PlainLogFile::deserialize($data['logFile']);
        if (!$log_file) {
            $log_file = GzLogFile::deserialize($data['logFile']);
        }
        $line_location = new LineLocation($log_file, $data['lineNumber']);
        $mode = $data['mode'];
        return [
            'lineLocation' => $line_location,
            'mode' => $mode,
        ];
    }
}
