<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;
use PhpTypeScriptApi\Fields\FieldTypes;

StandardSession::session_start_if_cookie_set();

$code_href = EnvUtils::fromEnv()->getCodeHref();
$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'token' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$new_url = "{$code_href}email_reaktion";
http_response_code(301);
header("Location: {$new_url}");
