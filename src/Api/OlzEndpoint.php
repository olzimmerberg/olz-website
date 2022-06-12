<?php

namespace Olz\Api;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Endpoint;

abstract class OlzEndpoint extends Endpoint {
    use \Psr\Log\LoggerAwareTrait;
    use WithUtilsTrait;

    public function runtimeSetup() {
        $this->populateFromEnv();
    }
}
