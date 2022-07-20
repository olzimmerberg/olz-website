<?php

namespace Olz\Apps\GoogleSearch;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\GoogleSearch\Endpoints\GetAppGoogleSearchCredentialsEndpoint;
use PhpTypeScriptApi\Api;

class GoogleSearchEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getAppGoogleSearchCredentials', function () {
            return new GetAppGoogleSearchCredentialsEndpoint();
        });
    }
}
