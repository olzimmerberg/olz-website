<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;

/** @extends HttpParams<array{}> */
class IndexParams extends HttpParams {
}

$env_utils = EnvUtils::fromEnv();
$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));

$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams(IndexParams::class, $_GET);

// TODO: Delete; Both index.php and startseite.php are obsolete!
$http_utils->redirect("{$env_utils->getCodeHref()}startseite", 308);
