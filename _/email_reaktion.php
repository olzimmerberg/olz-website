<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;

/** @extends HttpParams<array{token?: ?string}> */
class EmailReaktionParams extends HttpParams {
}

StandardSession::session_start_if_cookie_set();

$code_href = EnvUtils::fromEnv()->getCodeHref();
$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams(EmailReaktionParams::class, $_GET);

$token = $_GET['token'] ?? '';
$new_url = "{$code_href}email_reaktion?token={$token}";
http_response_code(301);
header("Location: {$new_url}");
