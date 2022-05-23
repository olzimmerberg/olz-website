<?php

require_once __DIR__.'/../utils/env/EnvUtils.php';

$olz_api = require __DIR__.'/olz_api.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger('OlzApi');
$olz_api->setLogger($logger);

$olz_api->serve();
