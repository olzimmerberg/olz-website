<?php

use Olz\Utils\HttpUtils;

$http_utils = HttpUtils::fromEnv();
$http_utils->redirect('/', 301);
