<?php

use Olz\Utils\EnvUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$new_url = "{$code_href}konto_passwort";
http_response_code(301);
header("Location: {$new_url}");
