<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/anmelden/endpoints/GetPrefillValuesEndpoint.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \GetPrefillValuesEndpoint
 */
final class GetPrefillValuesEndpointTest extends UnitTestCase {
    public function testGetPrefillValuesEndpointIdent(): void {
        $endpoint = new GetPrefillValuesEndpoint();
        $this->assertSame('GetPrefillValuesEndpoint', $endpoint->getIdent());
    }
}
