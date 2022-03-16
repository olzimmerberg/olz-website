<?php

/* Copy this file to ./config.php and fill in info for local MySQL server. */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$configure_env_utils = function ($env_utils) {
    $env_utils->configure([
        'mysql_host' => 'localhost',
        'mysql_port' => '3306',
        'mysql_username' => 'db-username',
        'mysql_password' => 'db-password',
        'mysql_schema' => 'db-schema',

        'has_unlimited_cron' => true,

        'date_utils_class_name' => 'FixedDateUtils',
        'date_utils_class_args' => ['2020-08-15 12:51:00'],

        'database_backup_key' => 'aaaaaaaaaaaaaaaaaaaa',
        'email_reaction_key' => 'aaaaaaaaaaaaaaaaaaab',
        'id_encryption_key' => 'aaaaaaaaaaaaaaaaaaac',

        'cron_authenticity_code' => 'this_is_just_local',

        'strava_client_id' => '123456',
        'strava_client_secret' => '0123456789abcdef0123456789abcdef01234567',

        'google_client_id' => '12345678901-0123456789abcdefghijklmnopqrstuv.apps.googleusercontent.com',
        'google_client_secret' => '0123456789abcdef01234567',

        'facebook_app_id' => '1234567890123456',
        'facebook_app_secret' => '0123456789abcdef0123456789abcdef',

        'telegram_bot_name' => 'olz_bot',
        'telegram_bot_token' => '123456789:abcdefghijklmnopqrstuvwxyz123456789',
        'telegram_authenticity_code' => 'this_is_just_local',

        'smtp_host' => 'localhost',
        'smtp_port' => '465',
        'smtp_username' => 'fake-user@olzimmerberg.ch',
        'smtp_password' => '1234',
        'smtp_from' => 'fake-user@olzimmerberg.ch',
    ]);
};
if (isset($_CONFIG)) {
    $configure_env_utils($_CONFIG);
}
return [
    'configure_env_utils' => $configure_env_utils,
];
