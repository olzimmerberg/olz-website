<?php

// =============================================================================
// Server-spezifische Konfiguration. Überall wo der Code ausgeführt werden soll,
// z.B. Production, Staging, Dev-Server, Integration-Test-Server, muss eine
// Datei `config.php` vorhanden sein, die von hier aus importiert wird.
// =============================================================================

class ServerConfig {
    private $mysql_host;
    private $mysql_port;
    private $mysql_username;
    private $mysql_password;
    private $mysql_schema;

    private $has_unlimited_cron = false;

    private $date_utils_class_name = 'LiveDateUtils';
    private $date_utils_class_args = [];

    private $database_backup_key;

    private $strava_client_id;
    private $strava_client_secret;

    private $google_client_id;
    private $google_client_secret;

    private $facebook_app_id;
    private $facebook_app_secret;

    private $telegram_bot_name;
    private $telegram_bot_token;
    private $telegram_authenticity_code;

    public function configure($config_dict) {
        $this->mysql_host = $config_dict['mysql_host'] ?? $this->mysql_host;
        $this->mysql_port = $config_dict['mysql_port'] ?? $this->mysql_port;
        $this->mysql_username = $config_dict['mysql_username'] ?? $this->mysql_username;
        $this->mysql_password = $config_dict['mysql_password'] ?? $this->mysql_password;
        $this->mysql_schema = $config_dict['mysql_schema'] ?? $this->mysql_schema;

        $this->has_unlimited_cron = $config_dict['has_unlimited_cron'] ?? $this->has_unlimited_cron;

        $this->date_utils_class_name = $config_dict['date_utils_class_name'] ?? $this->date_utils_class_name;
        $this->date_utils_class_args = $config_dict['date_utils_class_args'] ?? $this->date_utils_class_args;

        $this->database_backup_key = $config_dict['database_backup_key'] ?? $this->database_backup_key;

        $this->strava_client_id = $config_dict['strava_client_id'] ?? $this->strava_client_id;
        $this->strava_client_secret = $config_dict['strava_client_secret'] ?? $this->strava_client_secret;

        $this->google_client_id = $config_dict['google_client_id'] ?? $this->google_client_id;
        $this->google_client_secret = $config_dict['google_client_secret'] ?? $this->google_client_secret;

        $this->facebook_app_id = $config_dict['facebook_app_id'] ?? $this->facebook_app_id;
        $this->facebook_app_secret = $config_dict['facebook_app_secret'] ?? $this->facebook_app_secret;

        $this->telegram_bot_name = $config_dict['telegram_bot_name'] ?? $this->telegram_bot_name;
        $this->telegram_bot_token = $config_dict['telegram_bot_token'] ?? $this->telegram_bot_token;
        $this->telegram_authenticity_code = $config_dict['telegram_authenticity_code'] ?? $this->telegram_authenticity_code;
    }

    public function getMysqlHost() {
        return $this->mysql_host;
    }

    public function getMysqlPort() {
        return $this->mysql_port;
    }

    public function getMysqlServer() {
        return "{$this->mysql_host}:{$this->mysql_port}";
    }

    public function getMysqlUsername() {
        return $this->mysql_username;
    }

    public function getMysqlPassword() {
        return $this->mysql_password;
    }

    public function getMysqlSchema() {
        return $this->mysql_schema;
    }

    public function hasUnlimitedCron() {
        return $this->has_unlimited_cron;
    }

    public function getDateUtilsClassName() {
        return $this->date_utils_class_name;
    }

    public function getDateUtilsClassArgs() {
        return $this->date_utils_class_args;
    }

    public function getDatabaseBackupKey() {
        return $this->database_backup_key;
    }

    public function getStravaClientId() {
        return $this->strava_client_id;
    }

    public function getStravaClientSecret() {
        return $this->strava_client_secret;
    }

    public function getGoogleClientId() {
        return $this->google_client_id;
    }

    public function getGoogleClientSecret() {
        return $this->google_client_secret;
    }

    public function getFacebookAppId() {
        return $this->facebook_app_id;
    }

    public function getFacebookAppSecret() {
        return $this->facebook_app_secret;
    }

    public function getTelegramBotName() {
        return $this->telegram_bot_name;
    }

    public function getTelegramBotToken() {
        return $this->telegram_bot_token;
    }

    public function getTelegramAuthenticityCode() {
        return $this->telegram_authenticity_code;
    }
}

function getConfigPath() {
    $document_root = $_SERVER['DOCUMENT_ROOT'] ?? null;
    if ($document_root) {
        return $document_root.'/config.php';
    }
    // e.g. for doctrine cli-config.php
    return __DIR__.'/../../dev-server/config.php';
}

function assertValidExecutionContext() {
    $argv = $_SERVER['argv'] ?? [];
    $first_arg = $argv[0] ?? '';
    $is_phpunit = preg_match('/phpunit$/', $first_arg);
    $last_arg = $argv[count($argv) - 1] ?? '';
    $executing_unit_tests = preg_match('/unit_tests$/', $last_arg);
    if ($is_phpunit && $executing_unit_tests) {
        throw new \Exception('Unit tests should never import config/*');
    }
}

assertValidExecutionContext();

$config_path = getConfigPath();
if (!is_file($config_path)) {
    echo 'Config file not found';
    exit(1);
}

global $_CONFIG;

$_CONFIG = new ServerConfig();

require_once $config_path;
