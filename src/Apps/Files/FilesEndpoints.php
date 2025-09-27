<?php

namespace Olz\Apps\Files;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint;
use Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint;
use PhpTypeScriptApi\Api;

class FilesEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected GetWebdavAccessTokenEndpoint $getWebdavAccessTokenEndpoint,
        protected RevokeWebdavAccessTokenEndpoint $revokeWebdavAccessTokenEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('getWebdavAccessToken', $this->getWebdavAccessTokenEndpoint);
        $api->registerEndpoint('revokeWebdavAccessToken', $this->revokeWebdavAccessTokenEndpoint);
    }
}
