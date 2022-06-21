<?php

declare(strict_types=1);

use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;

require_once __DIR__.'/../../../common/UnitTestCase.php';

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
