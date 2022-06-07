<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));

$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([], $_GET);

$http_utils->redirect("{$env_utils->getCodeHref()}startseite.php", 308);
