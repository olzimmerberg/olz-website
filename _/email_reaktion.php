<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{token?: ?string}> */
class EmailReaktionParams extends HttpParams {
}

Session::session_start_if_cookie_set();

$code_href = EnvUtils::fromEnv()->getCodeHref();
$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(EmailReaktionParams::class, $_GET);

$token = $_GET['token'] ?? '';
$new_url = "{$code_href}email_reaktion?token={$token}";
$http_utils->redirect($new_url, 410);
