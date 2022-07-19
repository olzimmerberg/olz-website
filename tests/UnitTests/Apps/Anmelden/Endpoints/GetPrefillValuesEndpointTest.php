<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 * @covers \Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint
 */
final class GetPrefillValuesEndpointTest extends UnitTestCase {
    public function testGetPrefillValuesEndpointIdent(): void {
        $endpoint = new GetPrefillValuesEndpoint();
        $this->assertSame('GetPrefillValuesEndpoint', $endpoint->getIdent());
    }
}
