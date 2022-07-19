<?php

declare(strict_types=1);

use Olz\Api\OlzApi;
use PhpTypeScriptApi\Endpoint;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * covers different Classes!
 * @coversNothing
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        $olz_api = OlzApi::getInstance();

        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $endpoint->setup();
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }
}
