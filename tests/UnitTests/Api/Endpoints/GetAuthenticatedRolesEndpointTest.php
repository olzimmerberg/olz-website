<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetAuthenticatedRolesEndpoint;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\GetAuthenticatedRolesEndpoint
 */
final class GetAuthenticatedRolesEndpointTest extends UnitTestCase {
    public function testGetAuthenticatedRolesEndpoint(): void {
        WithUtilsCache::get('authUtils')->authenticated_roles = [
            FakeRole::adminRole(),
            FakeRole::defaultRole(),
        ];
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $endpoint->runtimeSetup();

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

    public function testGetAuthenticatedRolesEndpointNoRoles(): void {
        WithUtilsCache::get('authUtils')->authenticated_roles = [];
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['roles' => []], $result);
    }

    public function testGetAuthenticatedRolesEndpointUnauthenticated(): void {
        $endpoint = new GetAuthenticatedRolesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['roles' => null], $result);
    }
}
