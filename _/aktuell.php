<?php

use Olz\News\Utils\NewsUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{
 *   id?: ?numeric-string,
 *   filter?: ?string,
 * }> */
class AktuellParams extends HttpParams {
}

Session::session_start_if_cookie_set();

$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(AktuellParams::class, $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_utils = NewsUtils::fromEnv();
$filter = json_decode($_GET['filter'] ?? '{}', true);
if (!$news_utils->isValidFilter($filter)) {
    $filter = $news_utils->getDefaultFilter();
}
$enc_json_filter = urlencode(json_encode($filter) ?: '{}');

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}news?filter={$enc_json_filter}";
    $http_utils->redirect($new_url, 410);
} else {
    $new_url = "{$code_href}news/{$id}?filter={$enc_json_filter}";
    $http_utils->redirect($new_url, 410);
}
