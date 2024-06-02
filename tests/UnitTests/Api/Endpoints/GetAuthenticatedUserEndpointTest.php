<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetAuthenticatedUserEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeGetAuthenticatedUserEndpointAuthUtils {
    public function getCurrentUser(): ?User {
        return null;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\GetAuthenticatedUserEndpoint
 */
final class GetAuthenticatedUserEndpointTest extends UnitTestCase {
    public function testGetAuthenticatedUserEndpointIdent(): void {
        $endpoint = new GetAuthenticatedUserEndpoint();
        $this->assertSame('GetAuthenticatedUserEndpoint', $endpoint->getIdent());
    }

    public function testGetAuthenticatedUserEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $endpoint = new GetAuthenticatedUserEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'user' => [
                'id' => 1,
                'firstName' => 'Default',
                'lastName' => 'User',
                'username' => 'default',
            ],
        ], $result);
    }

    public function testGetAuthenticatedUserEndpointUnauthenticated(): void {
        $endpoint = new GetAuthenticatedUserEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['user' => null], $result);
    }
}
