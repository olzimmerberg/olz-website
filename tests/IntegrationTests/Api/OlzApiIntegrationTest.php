<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Api;

use Olz\Api\OlzApi;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use PhpTypeScriptApi\Endpoint;
use PhpTypeScriptApi\TypedEndpoint;
use Psr\Log\NullLogger;

/**
 * @internal
 * covers different Classes!
 *
 * @coversNothing
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        $olz_api = $this->getSut();

        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $endpoint?->setLogger(new NullLogger());
            $endpoint?->setup();
            $this->assertTrue(
                $endpoint instanceof Endpoint
                || $endpoint instanceof TypedEndpoint
            );
        }
    }

    protected function getSut(): OlzApi {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(OlzApi::class);
    }
}
