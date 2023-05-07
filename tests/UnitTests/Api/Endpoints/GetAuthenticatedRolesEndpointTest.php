<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetAuthenticatedRolesEndpoint;
use Olz\Tests\Fake;
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
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $endpoint->setLog($logger);

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
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
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['roles' => null], $result);
    }
}
