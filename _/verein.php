<?php

use Olz\Utils\DbUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

$entityManager = DbUtils::fromEnv()->getEntityManager();
$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'ressort' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

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
