<?php

/* Copy this file to ./olz.dev.php and fill in info for local MySQL server. */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$configure_env_utils = function ($env_utils) {
    $env_utils->configure([
        'syslog_path' => __DIR__.'/../public/logs/server/',
        'base_href' => 'http://127.0.0.1:30270',
        'app_env' => 'dev',

        'mysql_host' => 'localhost',
        'mysql_port' => '3306',
        'mysql_username' => 'db-username',
        'mysql_password' => 'db-password',
        'mysql_schema' => 'db-schema_test',

        'has_unlimited_cron' => true,

        'date_utils_class_name' => 'FixedDateUtils',
        'date_utils_class_args' => ['2020-08-15 12:51:00'],

        'database_backup_key' => 'aaaaaaaaaaaaaaaaaaaa',
        'email_reaction_key' => 'aaaaaaaaaaaaaaaaaaab',
        'id_encryption_key' => 'aaaaaaaaaaaaaaaaaaac',

        'cron_authenticity_code' => 'this_is_just_local',

        'recaptcha_secret_key' => 'abcdefghijklmnopqrstuvw-abcdefghijklmnop',

        'strava_client_id' => '123456',
        'strava_client_secret' => '0123456789abcdef0123456789abcdef01234567',

        'google_client_id' => '12345678901-0123456789abcdefghijklmnopqrstuv.apps.googleusercontent.com',
        'google_client_secret' => '0123456789abcdef01234567',

        'facebook_app_id' => '1234567890123456',
        'facebook_app_secret' => '0123456789abcdef0123456789abcdef',

        'telegram_bot_name' => 'olz_bot',
        'telegram_bot_token' => '123456789:abcdefghijklmnopqrstuvwxyz123456789',
        'telegram_authenticity_code' => 'this_is_just_local',

        'imap_host' => 'localhost',
        'imap_port' => '143',
        'imap_flags' => '/notls',
        'imap_username' => 'fake-user@olzimmerberg.ch',
        'imap_password' => '1234',

        'smtp_host' => 'localhost',
        'smtp_port' => '465',
        'smtp_username' => 'fake-user@olzimmerberg.ch',
        'smtp_password' => '1234',
        'smtp_secure' => 'ssl',
        'smtp_debug' => 0,
        'smtp_from' => 'fake-user@olzimmerberg.ch',

        'app_google_search_username' => 'fake-user@gmail.com',
        'app_google_search_password' => 'zxcv',

        'app_monitoring_username' => 'fake-user',
        'app_monitoring_password' => 'asdf',

        'app_statistics_username' => 'fake-user',
        'app_statistics_password' => 'qwer',
    ]);
};

return [
    'configure_env_utils' => $configure_env_utils,
];
