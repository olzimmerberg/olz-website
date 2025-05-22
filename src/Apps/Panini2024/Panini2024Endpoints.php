<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Panini2024\Endpoints\ListPanini2024PicturesEndpoint;
use Olz\Apps\Panini2024\Endpoints\UpdateMyPanini2024Endpoint;
use PhpTypeScriptApi\Api;

class Panini2024Endpoints extends BaseAppEndpoints {
    public function __construct(
        protected ListPanini2024PicturesEndpoint $listPanini2024PicturesEndpoint,
        protected UpdateMyPanini2024Endpoint $updateMyPanini2024Endpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('listPanini2024Pictures', $this->listPanini2024PicturesEndpoint);
        $api->registerEndpoint('updateMyPanini2024', $this->updateMyPanini2024Endpoint);
    }
}
