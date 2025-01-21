<?php

use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;

/** @extends HttpParams<array{
 *   id?: ?numeric-string,
 *   jahr?: ?numeric-string,
 *   archiv?: ?bool,
 *   buttongalerie?: ?string,
 * }> */
class GalerieParams extends HttpParams {
}

$db = DbUtils::fromEnv()->getDb();

StandardSession::session_start_if_cookie_set();

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams(GalerieParams::class, $_GET);

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
