<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{
 *   id?: ?numeric-string,
 *   buttonkarten?: ?string,
 * }> */
class KartenParams extends HttpParams {
}

Session::session_start_if_cookie_set();

$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(KartenParams::class, $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$new_url = "{$code_href}karten";
http_response_code(301);
header("Location: {$new_url}");
