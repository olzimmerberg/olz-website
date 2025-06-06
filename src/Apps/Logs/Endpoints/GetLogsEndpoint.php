<?php

namespace Olz\Apps\Logs\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Apps\Logs\Utils\GzLogFile;
use Olz\Apps\Logs\Utils\HybridLogFile;
use Olz\Apps\Logs\Utils\LineLocation;
use Olz\Apps\Logs\Utils\LogsDefinitions;
use Olz\Apps\Logs\Utils\PlainLogFile;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDateTime;

/**
 * @phpstan-type OlzLogLevel 'debug'|'info'|'notice'|'warning'|'error'|'critical'|'alert'|'emergency'
 * @phpstan-type OlzLogsQuery array{
 *   channel: string,
 *   targetDate?: ?IsoDateTime,
 *   firstDate?: ?IsoDateTime,
 *   lastDate?: ?IsoDateTime,
 *   minLogLevel?: ?OlzLogLevel,
 *   textSearch?: ?string,
 *   pageToken?: ?string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{query: OlzLogsQuery},
 *   array{content: array<string>, pagination: array{previous: ?string, next: ?string}},
 * >
 */
class GetLogsEndpoint extends OlzTypedEndpoint {
    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerApiObject(IsoDateTime::class);
    }

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Logs access by {$user?->getUsername()}.");

        $channel = null;
        foreach (LogsDefinitions::getLogsChannels() as $current_channel) {
            if ($current_channel::getId() === $input['query']['channel']) {
                $channel = new $current_channel();
            }
        }
        if (!$channel) {
            throw new HttpError(404, "Channel not found");
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
            'comparison' => $line_location->comparison,
            'mode' => $mode,
        ]) ?: null;
    }

    /** @return array{lineLocation: LineLocation, mode: string} */
    protected function deserializePageToken(
        string $serialized,
    ): array {
        $data = json_decode($serialized, true);
        $log_file_classes = [
            HybridLogFile::class,
            GzLogFile::class,
            PlainLogFile::class,
        ];
        $log_file = null;
        foreach ($log_file_classes as $log_file_class) {
            if (!$log_file) {
                $log_file = $log_file_class::deserialize($data['logFile']);
            }
        }
        $this->generalUtils()->checkNotNull($log_file, "No log file: {$data['logFile']}");
        $line_location = new LineLocation($log_file, $data['lineNumber'], $data['comparison']);
        $mode = $data['mode'];
        return [
            'lineLocation' => $line_location,
            'mode' => $mode,
        ];
    }
}
