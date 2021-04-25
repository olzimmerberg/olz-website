<?php

require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../config/server.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/dev_data.php';
require_once __DIR__.'/deploy_cleanup.php';
require_once __DIR__.'/doctrine_migrations.php';
require_once __DIR__.'/monitoring/backup_monitoring.php';

function run_tools($command_config, $server) {
    global $db, $data_path, $deploy_path, $_CONFIG;
    set_time_limit(600); // This might take some time...
    $has_command = preg_match('/^\/([a-z0-9\-]+)$/i', $server['PATH_INFO'], $matches);
    $command = $has_command ? $matches[1] : null;
    if (!isset($command_config[$command])) {
        $command = null;
    }
    if ($command) {
        header('Content-Type: text/plain;charset=utf8');
    }
    if ($command === 'reset') {
        return run_command($command, 'reset_db', [$db, $data_path]);
    }
    if ($command === 'dump') {
        return run_command($command, 'dump_db', [$db]);
    }
    if ($command === 'get-database-backup') {
        return run_command($command, 'get_database_backup', [$db, $_CONFIG->getDatabaseBackupKey()]);
    }
    if ($command === 'deploy-cleanup') {
        return run_command($command, 'deploy_cleanup', [$deploy_path]);
    }
    if ($command === 'migrate') {
        return run_command($command, 'migrate_to_latest', []);
    }
    if ($command === 'backup-monitoring') {
        return run_command($command, 'backup_monitoring', []);
    }
    // No command to execute => show index
    echo "<h1>Tools</h1>";
    echo "<h2>Available commands</h2>";
    $base_uri = $server['REQUEST_URI'];
    foreach ($command_config as $key => $value) {
        echo "<p><a href='{$base_uri}/{$key}'><code>{$key}</code></a></p>";
    }
}

function run_command($command, $callback, $args) {
    try {
        if (!is_callable($callback)) {
            throw new Exception('callback not callable');
        }
        if (!is_array($args)) {
            throw new Exception('args is not an array');
        }
        call_user_func_array($callback, $args);
        echo "{$command}:SUCCESS";
    } catch (Exception $exc) {
        http_response_code(500);
        echo "{$command}:ERROR\n";
        echo $exc->getMessage()."\n";
        require_once __DIR__.'/../utils/GeneralUtils.php';
        $general_utils = GeneralUtils::fromEnv();
        echo $general_utils->getPrettyTrace($exc->getTrace());
    }
}
