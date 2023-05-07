<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetAuthenticatedUserEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeGetAuthenticatedUserEndpointAuthUtils {
    public function getCurrentUser() {
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
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::defaultUser();
        $endpoint = new GetAuthenticatedUserEndpoint();

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
                'username' => 'user',
            ],
        ], $result);
    }

    public function testGetAuthenticatedUserEndpointUnauthenticated(): void {
        $endpoint = new GetAuthenticatedUserEndpoint();

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['user' => null], $result);
    }
}
