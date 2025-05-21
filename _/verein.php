<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{ressort?: ?string}> */
class VereinParams extends HttpParams {
}

Session::session_start_if_cookie_set();

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(VereinParams::class, $_GET);

if (isset($_GET['ressort'])) {
    $ressort = $_GET['ressort'];
    $new_url = "{$code_href}verein/{$ressort}";
    http_response_code(301);
    header("Location: {$new_url}");
} else {
    $new_url = "{$code_href}verein";
    http_response_code(301);
    header("Location: {$new_url}");
}
