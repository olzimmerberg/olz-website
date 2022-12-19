<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetAuthenticatedUserEndpoint;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\Fake\FakeUsers;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class FakeGetAuthenticatedUserEndpointAuthUtils {
    public function getAuthenticatedUser() {
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
        $auth_utils = new FakeAuthUtils();
        $auth_utils->authenticated_user = FakeUsers::adminUser();
        $logger = FakeLogger::create();
        $endpoint = new GetAuthenticatedUserEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'user' => [
                'id' => 2,
                'firstName' => 'Admin',
                'lastName' => 'Istrator',
                'username' => 'admin',
            ],
        ], $result);
    }

    public function testGetAuthenticatedUserEndpointUnauthenticated(): void {
        $auth_utils = new FakeGetAuthenticatedUserEndpointAuthUtils();
        $logger = FakeLogger::create();
        $endpoint = new GetAuthenticatedUserEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call(null);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['user' => null], $result);
    }
}
