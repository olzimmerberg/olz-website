<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Panini2024\Endpoints\ListPanini2024PicturesEndpoint;
use Olz\Apps\Panini2024\Endpoints\UpdateMyPanini2024Endpoint;
use PhpTypeScriptApi\Api;

class Panini2024Endpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('listPanini2024Pictures', function () {
            return new ListPanini2024PicturesEndpoint();
        });
        $api->registerEndpoint('updateMyPanini2024', function () {
            return new UpdateMyPanini2024Endpoint();
        });
    }
}
