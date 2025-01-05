<?php

namespace Olz\Apps\Import;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Import\Endpoints\ImportTermineEndpoint;
use PhpTypeScriptApi\Api;

class ImportEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('importTermine', function () {
            return new ImportTermineEndpoint();
        });
    }
}
