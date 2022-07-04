<?php

namespace Olz\Apps\Logs;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Logs\Endpoints\GetLogsEndpoint;
use PhpTypeScriptApi\Api;

class LogsEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getLogs', function () {
            return new GetLogsEndpoint();
        });
    }
}
