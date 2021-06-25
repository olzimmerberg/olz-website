<?php

require_once __DIR__.'/utils/client/HttpUtils.php';
require_once __DIR__.'/utils/env/EnvUtils.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->redirect("{$env_utils->getCodeHref()}startseite.php", 308);
