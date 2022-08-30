<?php

namespace Olz\Apps\Youtube;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Youtube\Endpoints\GetAppYoutubeCredentialsEndpoint;
use PhpTypeScriptApi\Api;

class YoutubeEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getAppYoutubeCredentials', function () {
            return new GetAppYoutubeCredentialsEndpoint();
        });
    }
}
