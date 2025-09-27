<?php

namespace Olz\Apps\Monitoring;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Monitoring\Endpoints\GetAppMonitoringCredentialsEndpoint;
use PhpTypeScriptApi\Api;

class MonitoringEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected GetAppMonitoringCredentialsEndpoint $getAppMonitoringCredentialsEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('getAppMonitoringCredentials', $this->getAppMonitoringCredentialsEndpoint);
    }
}
