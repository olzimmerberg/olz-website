<?php

namespace Olz\Apps;

use PhpTypeScriptApi\Api;

abstract class BaseAppEndpoints {
    abstract public function register(Api $api): void;
}
