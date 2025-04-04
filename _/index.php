<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;

/** @extends HttpParams<array{}> */
class IndexParams extends HttpParams {
}

$env_utils = EnvUtils::fromEnv();

$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(IndexParams::class, $_GET);

// TODO: Delete; Both index.php and startseite.php are obsolete!
$http_utils->redirect("{$env_utils->getCodeHref()}startseite", 308);
