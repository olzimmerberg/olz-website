<?php

use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'filter' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_filter_utils = NewsFilterUtils::fromEnv();
$filter = json_decode($_GET['filter'] ?? '{}', true);
if (!$news_filter_utils->isValidFilter($filter)) {
    $filter = $news_filter_utils->getDefaultFilter();
}
$enc_json_filter = urlencode(json_encode($filter));

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}news?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
} else {
    $new_url = "{$code_href}news/{$id}?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
}
