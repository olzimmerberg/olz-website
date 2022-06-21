<?php

declare(strict_types=1);

use Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint;

require_once __DIR__.'/../../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint
 */
final class GetManagedUsersEndpointTest extends UnitTestCase {
    public function testGetManagedUsersEndpointIdent(): void {
        $endpoint = new GetManagedUsersEndpoint();
        $this->assertSame('GetManagedUsersEndpoint', $endpoint->getIdent());
    }
}
