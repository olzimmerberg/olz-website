<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;
use PhpTypeScriptApi\Fields\FieldTypes;

StandardSession::session_start_if_cookie_set();

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'buttonlinks' => new FieldTypes\StringField(['allow_null' => true]),
    'buttondownloads' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$new_url = "{$code_href}service";
http_response_code(301);
header("Location: {$new_url}");
