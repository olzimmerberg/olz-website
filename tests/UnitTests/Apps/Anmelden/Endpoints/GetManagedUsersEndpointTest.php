<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

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
