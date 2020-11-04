<?php

/* Copy this file to ./config.php and fill in info for local MySQL server. */

$MYSQL_SERVER = "localhost:3306";
$MYSQL_USERNAME = "db-username";
$MYSQL_PASSWORD = "db-password";
$MYSQL_SCHEMA = "db-schema";

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$_CONFIG->unlimited_cron = true;

$DATABASE_BACKUP_KEY = 'aaaaaaaaaaaaaaaaaaaa';

$STRAVA_CLIENT_ID = '123456';
$STRAVA_CLIENT_SECRET = '0123456789abcdef0123456789abcdef01234567';
