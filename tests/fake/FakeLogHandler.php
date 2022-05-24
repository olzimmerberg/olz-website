<?php

require_once __DIR__.'/FakeEnvUtils.php';

class FakeLogHandler implements Monolog\Handler\HandlerInterface {
    public $records = [];

    public function isHandling(array $args): bool {
        return true;
    }

    public function handle(array $record): bool {
        $this->records[] = $record;
        return true;
    }

    public function handleBatch(array $records): void {
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
                $level_name = $record['level_name'];
                $message = str_replace(
                    [
                        $data_path,
                        $data_realpath,
                    ],
                    [
                        'data-path/',
                        'data-realpath/',
                    ],
                    $record['message']
                );
                return $map_fn($record, $level_name, $message);
            },
            $this->records
        );
    }
}
