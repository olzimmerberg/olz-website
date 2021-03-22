<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/api/OlzApi.php';
require_once __DIR__.'/../../../src/api/common/Endpoint.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \OlzApi
 */
final class OlzApiTest extends UnitTestCase {
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
