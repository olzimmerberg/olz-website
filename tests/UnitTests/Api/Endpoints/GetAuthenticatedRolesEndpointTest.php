<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetAuthenticatedRolesEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class FakeGetAuthenticatedRolesEndpointAuthUtils {
    public function getAuthenticatedRoles() {
        return null;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\GetAuthenticatedRolesEndpoint
 */
final class GetAuthenticatedRolesEndpointTest extends UnitTestCase {
    public function testGetAuthenticatedRolesEndpointIdent(): void {
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $this->assertSame('GetAuthenticatedRolesEndpoint', $endpoint->getIdent());
    }

    public function testGetAuthenticatedRolesEndpoint(): void {
        $endpoint = new GetAuthenticatedRolesEndpoint();

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'roles' => [
                [
                    'id' => 2,
                    'name' => 'Administrator',
                    'username' => 'admin_role',
                ],
                [
                    'id' => 1,
                    'name' => 'Default',
                    'username' => 'role',
                ],
            ],
        ], $result);
    }

    public function testGetAuthenticatedRolesEndpointUnauthenticated(): void {
        $auth_utils = new FakeGetAuthenticatedRolesEndpointAuthUtils();
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $endpoint->setAuthUtils($auth_utils);

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['roles' => null], $result);
    }
}
