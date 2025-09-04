<?php

use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

/** @extends HttpParams<array{
 *   id?: ?numeric-string,
 *   buttonblog?: ?string,
 * }> */
class BlogParams extends HttpParams {
}

Session::session_start_if_cookie_set();
$http_utils = HttpUtils::fromEnv();
$http_utils->validateGetParams(BlogParams::class, $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_filter_utils = NewsFilterUtils::fromEnv();
$filter = $news_filter_utils->getDefaultFilter();
$filter['format'] = 'kaderblog';
$enc_json_filter = urlencode(json_encode($filter) ?: '{}');
$new_url = "{$code_href}news?filter={$enc_json_filter}";
$http_utils->redirect($new_url, 410);
