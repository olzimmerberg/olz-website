<?php

declare(strict_types=1);

use PhpTypeScriptApi\Endpoint;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * covers different Classes!
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        $olz_api = require __DIR__.'/../../../public/_/api/olz_api.php';

        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $endpoint->setup();
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }
}
