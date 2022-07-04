<?php

namespace Olz\Apps\Files;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint;
use Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint;
use PhpTypeScriptApi\Api;

class FilesEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getWebdavAccessToken', function () {
            return new GetWebdavAccessTokenEndpoint();
        });
        $api->registerEndpoint('revokeWebdavAccessToken', function () {
            return new RevokeWebdavAccessTokenEndpoint();
        });
    }
}
