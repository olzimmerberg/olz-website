<?php

use Olz\News\Utils\NewsUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{
 *   id?: ?numeric-string,
 *   jahr?: ?numeric-string,
 *   archiv?: ?bool,
 *   buttongalerie?: ?string,
 * }> */
class GalerieParams extends HttpParams {
}

Session::session_start_if_cookie_set();

$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(GalerieParams::class, $_GET);

if (isset($_GET['datum']) || isset($_GET['foto'])) {
    $http_utils->dieWithHttpError(404);
    throw new Exception('should already have failed');
}

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_utils = NewsUtils::fromEnv();
$filter = $news_utils->getDefaultFilter();
$filter['format'] = 'galerie';
$enc_json_filter = urlencode(json_encode($filter) ?: '{}');

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}news?filter={$enc_json_filter}";
    $http_utils->redirect($new_url, 410);
} else {
    $news_id = $id + 1200;
    $new_url = "{$code_href}news/{$news_id}?filter={$enc_json_filter}";
    $http_utils->redirect($new_url, 410);
}
