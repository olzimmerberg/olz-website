<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;

$env_utils = EnvUtils::fromEnv();
$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));

$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([], $_GET);

$http_utils->redirect("{$env_utils->getCodeHref()}startseite.php", 308);
