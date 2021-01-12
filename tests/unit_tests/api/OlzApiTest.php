<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/api/OlzApi.php';
require_once __DIR__.'/../../../src/api/common/Endpoint.php';

/**
 * @internal
 * @covers \OlzApi
 */
final class OlzApiTest extends TestCase {
    public function testOlzApiHasEndpoints(): void {
        $endpoint = new OlzApi();
        $this->assertGreaterThan(0, count($endpoint->endpoints));
    }

    public function testOlzApiCanCreateAllEndpoints(): void {
        $endpoint = new OlzApi();
        foreach ($endpoint->endpoints as $endpoint_name => $endpoint_factory) {
            $endpoint = $endpoint_factory();
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }
}
