<?php

namespace Olz\Apps\SearchEngines;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\SearchEngines\Endpoints\GetAppSearchEnginesCredentialsEndpoint;
use PhpTypeScriptApi\Api;

class SearchEnginesEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getAppSearchEnginesCredentials', function () {
            return new GetAppSearchEnginesCredentialsEndpoint();
        });
    }
}
