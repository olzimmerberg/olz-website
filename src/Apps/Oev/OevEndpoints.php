<?php

namespace Olz\Apps\Oev;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Oev\Endpoints\SearchTransportConnectionEndpoint;
use PhpTypeScriptApi\Api;

class OevEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('searchTransportConnection', function () {
            return new SearchTransportConnectionEndpoint();
        });
    }
}
