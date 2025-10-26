<?php

use Olz\Termine\Utils\TermineUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\Session;

Session::session_start_if_cookie_set();

$http_utils = HttpUtils::fromEnv();
$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$termine_utils = TermineUtils::fromEnv()->loadTypeOptions();
$filter = json_decode($_GET['filter'] ?? '{}', true);
$filter_param = '';
if ($termine_utils->isValidFilter($filter)) {
    $serialized_filter = $termine_utils->serialize($filter);
    $filter_param = "?filter={$serialized_filter}";
}

$id = $_GET['id'] ?? null;

if ($id === null) {
    $new_url = "{$code_href}termine{$filter_param}";
    $http_utils->redirect($new_url, 410);
} else {
    $new_url = "{$code_href}termine/{$id}{$filter_param}";
    $http_utils->redirect($new_url, 410);
}
