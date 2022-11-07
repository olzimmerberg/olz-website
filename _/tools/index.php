<?php

use Olz\Utils\DevDataUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\GeneralUtils;
use Olz\Utils\LogsUtils;

require_once __DIR__.'/monitoring/backup_monitoring.php';
require_once __DIR__.'/monitoring/logs_monitoring.php';

function run_tools($command_config, $server) {
    set_time_limit(600); // This might take some time...
    $has_command = preg_match('/^\/([a-z0-9\-\_]+)$/i', $server['PATH_INFO'], $matches);
    $command = $has_command ? $matches[1] : null;
    if (!isset($command_config[$command])) {
        $command = null;
    }
    if ($command) {
        header('Content-Type: text/plain;charset=utf8');
    }
    if ($command === 'reset-content') {
        return run_command($command, function () {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->resetDbContent();
        });
    }
    if ($command === 'reset-structure') {
        return run_command($command, function () {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->resetDbStructure();
        });
    }
    if ($command === 'full-reset') {
        return run_command($command, function () {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->fullResetDb();
        });
    }
    if ($command === 'dump') {
        return run_command($command, function () {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->dumpDb();
        });
    }
    if ($command === 'get-database-backup') {
        return run_command($command, function () {
            $env_utils = EnvUtils::fromEnv();
            $key = $env_utils->getDatabaseBackupKey();
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->getDbBackup($key);
        });
    }
    if ($command === 'get-id-algos') {
        return run_command($command, function () {
            echo json_encode(openssl_get_cipher_methods())."\n";
        });
    }
    if ($command === 'migrate') {
        return run_command($command, function () {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->migrateTo('latest');
        });
    }
    if ($command === 'backup-monitoring') {
        return run_command($command, function () {
            backup_monitoring();
        });
    }
    if ($command === 'logs-monitoring') {
        return run_command($command, function () {
            logs_monitoring();
        });
    }
    // No command to execute => show index
    echo "<h1>Tools</h1>";
    echo "<h2>Available commands</h2>";
    $base_uri = $server['REQUEST_URI'];
    foreach ($command_config as $key => $value) {
        echo "<p><a href='{$base_uri}/{$key}'><code>{$key}</code></a></p>";
    }
}

function run_command($command, $fn) {
    $logger = LogsUtils::fromEnv()->getLogger("Tool:{$command}");
    LogsUtils::activateLogger($logger);
    try {
        if (!is_callable($fn)) {
            throw new \Exception('fn not callable');
        }
        $fn();
        echo "{$command}:SUCCESS";
    } catch (\Exception $exc) {
        http_response_code(500);
        echo "{$command}:ERROR\n";
        echo $exc->getMessage()."\n";
        $general_utils = GeneralUtils::fromEnv();
        $pretty_trace = $general_utils->getPrettyTrace($exc->getTrace());
        echo $pretty_trace;
        $logger->error("Tool {$command} failed with error: {$exc->getMessage()}", [$pretty_trace]);
    }
    LogsUtils::deactivateLogger($logger);
}
