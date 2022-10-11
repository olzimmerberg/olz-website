<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzApi;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class OlzApiTest extends UnitTestCase {
    public function testOlzApiHasEndpoints(): void {
        $olz_api = OlzApi::getInstance();
        $this->assertGreaterThan(0, count($olz_api->getEndpointNames()));
    }
}
