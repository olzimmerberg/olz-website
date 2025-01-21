<?php

use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use Olz\Utils\StandardSession;

/** @extends HttpParams<array{
 *   id?: ?numeric-string,
 *   code?: ?string,
 *   buttonforum?: ?string,
 * }> */
class ForumParams extends HttpParams {
}

StandardSession::session_start_if_cookie_set();

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams(ForumParams::class, $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_filter_utils = NewsFilterUtils::fromEnv();
$filter = $news_filter_utils->getDefaultFilter();
$filter['format'] = 'forum';
$enc_json_filter = urlencode(json_encode($filter));
$new_url = "{$code_href}news?filter={$enc_json_filter}";
http_response_code(301);
header("Location: {$new_url}");
