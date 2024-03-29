<?php

namespace Olz\Apps\Statistics;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint;
use PhpTypeScriptApi\Api;

class StatisticsEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getAppStatisticsCredentials', function () {
            return new GetAppStatisticsCredentialsEndpoint();
        });
    }
}
