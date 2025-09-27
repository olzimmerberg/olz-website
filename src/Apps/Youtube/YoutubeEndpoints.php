<?php

namespace Olz\Apps\Youtube;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Youtube\Endpoints\GetAppYoutubeCredentialsEndpoint;
use PhpTypeScriptApi\Api;

class YoutubeEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected GetAppYoutubeCredentialsEndpoint $getAppYoutubeCredentialsEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('getAppYoutubeCredentials', $this->getAppYoutubeCredentialsEndpoint);
    }
}
