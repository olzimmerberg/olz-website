<?php

declare(strict_types=1);

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \OlzApi
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        require_once __DIR__.'/../../../src/api/OlzApi.php';

        $endpoint = new OlzApi();
        foreach ($endpoint->endpoints as $endpoint_name => $endpoint_factory) {
            $endpoint = $endpoint_factory();
            $endpoint->setup();
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }
}
