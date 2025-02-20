<?php

use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__.'/../.env');

$kernel = new Olz\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
// @phpstan-ignore-next-line
return $kernel->getContainer()->get('doctrine')->getManager();
