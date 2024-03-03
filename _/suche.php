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
    'anfrage' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$anfrage = urlencode($_GET['anfrage'] ?? '');
$new_url = "{$code_href}suche".($anfrage ? "?anfrage={$anfrage}" : '');
http_response_code(301);
header("Location: {$new_url}");
