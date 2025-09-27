<?php

namespace Olz\Apps\Members;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Members\Endpoints\ExportMembersEndpoint;
use Olz\Apps\Members\Endpoints\ImportMembersEndpoint;
use PhpTypeScriptApi\Api;

class MembersEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected ImportMembersEndpoint $importMembersEndpoint,
        protected ExportMembersEndpoint $exportMembersEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('importMembers', $this->importMembersEndpoint);
        $api->registerEndpoint('exportMembers', $this->exportMembersEndpoint);
    }
}
