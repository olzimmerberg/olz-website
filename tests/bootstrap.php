<?php

use Symfony\Component\Dotenv\Dotenv;

date_default_timezone_set('Europe/Zurich');

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0o000);
}
