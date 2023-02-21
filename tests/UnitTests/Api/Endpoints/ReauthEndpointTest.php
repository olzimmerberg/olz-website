<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\ReauthEndpoint;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\ReauthEndpoint
 */
final class ReauthEndpointTest extends UnitTestCase {
    public function testReauthEndpointIdent(): void {
        $endpoint = new ReauthEndpoint();
        $this->assertSame('ReauthEndpoint', $endpoint->getIdent());
    }

    public function testReauthEndpointWithoutInput(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ReauthEndpoint();
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => ["Fehlender Schlüssel: usernameOrEmail."],
                'reauthToken' => ["Fehlender Schlüssel: reauthToken."],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testReauthEndpointWithNullInput(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ReauthEndpoint();
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([
                'usernameOrEmail' => null,
                'reauthToken' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => [['.' => ['Feld darf nicht leer sein.']]],
                'reauthToken' => [['.' => ['Feld darf nicht leer sein.']]],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testReauthEndpointWithCorrectToken(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $user = Fake\FakeUsers::adminUser();
        $auth_utils->authenticate_user = $user;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new ReauthEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'reauthToken' => 'valid-admin-reauth-token',
        ]);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
            'reauthToken' => 'replaced-reauth-token',
        ], $result);
        $this->assertSame([
            'auth' => 'all',
            'root' => 'karten',
            'user' => 'admin',
            'user_id' => 2,
        ], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastLoginAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(true, $entity_manager->flushed);
    }

    public function testReauthEndpointWithInvalidCredentials(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->authenticate_with_error = new InvalidCredentialsException('test');
        $logger = Fake\FakeLogger::create();
        $endpoint = new ReauthEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'wrooong',
            'reauthToken' => 'invalid-reauth-token',
        ]);

        $this->assertSame([
            'status' => 'INVALID_CREDENTIALS',
            'reauthToken' => null,
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testReauthEndpointCanNotAuthenticate(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->authenticate_with_error = new AuthBlockedException('test');
        $logger = Fake\FakeLogger::create();
        $endpoint = new ReauthEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'reauthToken' => 'valid-admin-reauth-token',
        ]);

        $this->assertSame([
            'status' => 'BLOCKED',
            'reauthToken' => null,
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }
}
