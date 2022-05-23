<?php

declare(strict_types=1);

use PhpTypeScriptApi\Endpoint;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class OlzApiTest extends UnitTestCase {
    public function testOlzApiHasEndpoints(): void {
        $olz_api = require __DIR__.'/../../../public/_/api/olz_api.php';
        $this->assertGreaterThan(0, count($olz_api->getEndpointNames()));
    }

    public function testOlzApiCanCreateAllEndpoints(): void {
        $olz_api = require __DIR__.'/../../../public/_/api/olz_api.php';
        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }
}
