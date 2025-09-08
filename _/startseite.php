<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

$env_utils = EnvUtils::fromEnv();
$http_utils = HttpUtils::fromEnv();

$new_url = "{$env_utils->getCodeHref()}";
$http_utils->redirect($new_url, 410);
