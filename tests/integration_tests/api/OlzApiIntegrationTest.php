<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/api/OlzApi.php';

/**
 * @internal
 * @covers \OlzApi
 */
final class OlzApiIntegrationTest extends TestCase {
    public function testCanSetupEachEndpoint(): void {
        $endpoint = new OlzApi();
        foreach ($endpoint->endpoints as $endpoint_name => $endpoint_factory) {
            $endpoint = $endpoint_factory();
            $endpoint->setup();
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }
}
