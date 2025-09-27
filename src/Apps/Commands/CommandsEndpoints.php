<?php

namespace Olz\Apps\Commands;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Commands\Endpoints\ExecuteCommandEndpoint;
use PhpTypeScriptApi\Api;

class CommandsEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected ExecuteCommandEndpoint $executeCommandEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('executeCommand', $this->executeCommandEndpoint);
    }
}
