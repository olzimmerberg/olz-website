<?php

/* Copy this file to ./olz.test.php and fill in info for local MySQL server. */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$configure_env_utils = function ($env_utils) {
    $env_utils->configure([
        'data_path' => __DIR__.'/../tests/IntegrationTests/document-root/',
        'private_path' => __DIR__.'/../private/',
        'syslog_path' => __DIR__.'/../private/logs/server/',
        'base_href' => 'http://integration-test.host',
        'app_env' => 'test',

        'mysql_host' => 'localhost',
        'mysql_port' => '3306',
        'mysql_username' => 'db-username',
        'mysql_password' => 'db-password',
        'mysql_schema' => 'db-schema_test',

        'has_unlimited_cron' => true,

        'date_utils_class_name' => 'FixedDateUtils',
        'date_utils_class_args' => ['2020-08-15 16:51:00'],

        'database_backup_key' => 'aaaaaaaaaaaaaaaaaaaa',
        'email_reaction_key' => 'aaaaaaaaaaaaaaaaaaab',
        'id_encryption_key' => 'aaaaaaaaaaaaaaaaaaac',

        'cron_authenticity_code' => 'this_is_just_local',

        'strava_client_id' => '123456',
        'strava_client_secret' => '0123456789abcdef0123456789abcdef01234567',

        'telegram_bot_name' => 'olz_bot',
        'telegram_bot_token' => '123456789:abcdefghijklmnopqrstuvwxyz123456789',
        'telegram_authenticity_code' => 'this_is_just_local',

        'smtp_host' => 'localhost',
        'smtp_port' => '465',
        'smtp_username' => 'fake-user@olzimmerberg.ch',
        'smtp_password' => '1234',
        'smtp_secure' => 'ssl',
        'smtp_debug' => 0,
        'smtp_from' => 'fake-user@olzimmerberg.ch',

        'email_forwarding_host' => 'staging.olzimmerberg.ch',
    ]);
};

return [
    'configure_env_utils' => $configure_env_utils,
];
