<?php

use Olz\Utils\EnvUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$new_url = "{$code_href}konto_telegram";
http_response_code(301);
header("Location: {$new_url}");
