<?php

namespace Olz\Apps\Results;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Results\Endpoints\UpdateResultsEndpoint;
use PhpTypeScriptApi\Api;

class ResultsEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected UpdateResultsEndpoint $updateResultsEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('updateResults', $this->updateResultsEndpoint);
    }
}
