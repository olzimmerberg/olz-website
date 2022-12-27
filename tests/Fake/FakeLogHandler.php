<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Monolog\Handler\HandlerInterface;
use Monolog\LogRecord;

class FakeLogHandler implements HandlerInterface {
    public $records = [];

    public function isHandling(LogRecord $args): bool {
        return true;
    }

    public function handle(LogRecord $record): bool {
        $this->records[] = $record;
        return true;
    }

    public function handleBatch(array $records): void {
        foreach ($records as $record) {
            $this->records[] = $record;
        }
    }

    public function close(): void {
    }

    public function getPrettyRecords($map_fn = null) {
        $env_utils = new FakeEnvUtils();
        $data_path = $env_utils->getDataPath();
        $data_realpath = realpath($data_path);
        if (!$map_fn) {
            $map_fn = function ($record, $level_name, $message) {
                return "{$level_name} {$message}";
            };
        }
        return array_map(
            function ($record) use ($data_path, $data_realpath, $map_fn) {
                $arr = $record->toArray();
                $level_name = $arr['level_name'];
                $message = str_replace(
                    [
                        $data_path,
                        $data_realpath,
                    ],
                    [
                        'data-path/',
                        'data-realpath/',
                    ],
                    $arr['message']
                );
                return $map_fn($record, $level_name, $message);
            },
            $this->records
        );
    }

    public function resetRecords(): void {
        $this->records = [];
    }
}
