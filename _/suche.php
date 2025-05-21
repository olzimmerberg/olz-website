<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{anfrage?: ?string}> */
class SucheParams extends HttpParams {
}

Session::session_start_if_cookie_set();

$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(SucheParams::class, $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$anfrage = urlencode($_GET['anfrage'] ?? '');
$new_url = "{$code_href}suche".($anfrage ? "?anfrage={$anfrage}" : '');
http_response_code(301);
header("Location: {$new_url}");
