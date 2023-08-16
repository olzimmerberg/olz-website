<?php

use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\EnvUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$termine_filter_utils = TermineFilterUtils::fromEnv();
$filter = json_decode($_GET['filter'] ?? '{}', true);
if (!$termine_filter_utils->isValidFilter($filter)) {
    $filter = $termine_filter_utils->getDefaultFilter();
}
$enc_json_filter = urlencode(json_encode($filter));

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}termine?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
} else {
    $new_url = "{$code_href}termine/{$id}?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
}
