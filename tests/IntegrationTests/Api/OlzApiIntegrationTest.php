<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Api;

use Olz\Api\OlzApi;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use PhpTypeScriptApi\Endpoint;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @internal
 * covers different Classes!
 *
 * @coversNothing
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        $olz_api = OlzApi::getShallowInstance();

        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $endpoint?->setup();
            $this->assertTrue(
                $endpoint instanceof Endpoint
                || $endpoint instanceof TypedEndpoint
            );
        }
    }
}
