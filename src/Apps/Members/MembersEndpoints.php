<?php

namespace Olz\Apps\Members;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Members\Endpoints\ExportMembersEndpoint;
use Olz\Apps\Members\Endpoints\ImportMembersEndpoint;
use PhpTypeScriptApi\Api;

class MembersEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('importMembers', function () {
            return new ImportMembersEndpoint();
        });
        $api->registerEndpoint('exportMembers', function () {
            return new ExportMembersEndpoint();
        });
    }
}
