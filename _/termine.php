<?php

use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\EnvUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$termine_filter_utils = TermineFilterUtils::fromEnv();
$filter = json_decode($_GET['filter'] ?? '{}', true);
$filter_param = '';
if ($termine_filter_utils->isValidFilter($filter)) {
    $enc_json_filter = urlencode(json_encode($filter));
    $filter_param = "?filter={$enc_json_filter}";
}

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}termine{$filter_param}";
    http_response_code(301);
    header("Location: {$new_url}");
} else {
    $new_url = "{$code_href}termine/{$id}{$filter_param}";
    http_response_code(301);
    header("Location: {$new_url}");
}
