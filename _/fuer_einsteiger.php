<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;

/** @extends HttpParams<array{
 *   von?: ?string,
 *   fbclid?: ?string,
 * }> */
class FuerEinsteigerParams extends HttpParams {
}

StandardSession::session_start_if_cookie_set();

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams(FuerEinsteigerParams::class, $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$new_url = "{$code_href}fuer_einsteiger";
http_response_code(301);
header("Location: {$new_url}");
