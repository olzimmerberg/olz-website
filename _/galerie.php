<?php

use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;
use PhpTypeScriptApi\Fields\FieldTypes;

$db = DbUtils::fromEnv()->getDb();

StandardSession::session_start_if_cookie_set();

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'jahr' => new FieldTypes\IntegerField(['allow_null' => true]),
    'archiv' => new FieldTypes\BooleanField(['allow_null' => true]),
    'buttongalerie' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

if (isset($_GET['datum']) || isset($_GET['foto'])) {
    $http_utils->dieWithHttpError(404);
}

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_filter_utils = NewsFilterUtils::fromEnv();
$filter = $news_filter_utils->getDefaultFilter();
$filter['format'] = 'galerie';
$enc_json_filter = urlencode(json_encode($filter));

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}news?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
} else {
    $news_id = $id + 1200;
    $new_url = "{$code_href}news/{$news_id}?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
}
