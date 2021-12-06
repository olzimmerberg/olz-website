<?php

declare(strict_types=1);

use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../src/api/endpoints/LoginEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \LoginEndpoint
 */
final class LoginEndpointTest extends UnitTestCase {
    public function testLoginEndpointIdent(): void {
        $endpoint = new LoginEndpoint();
        $this->assertSame('LoginEndpoint', $endpoint->getIdent());
    }

    public function testLoginEndpointWithoutInput(): void {
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => ["Fehlender Schlüssel: usernameOrEmail."],
                'password' => ["Fehlender Schlüssel: password."],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testLoginEndpointWithNullInput(): void {
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([
                'usernameOrEmail' => null,
                'password' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => [['.' => ['Feld darf nicht leer sein.']]],
                'password' => [['.' => ['Feld darf nicht leer sein.']]],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testLoginEndpointWithCorrectCredentials(): void {
        $auth_utils = new FakeAuthUtils();
        $user = FakeUsers::adminUser();
        $auth_utils->authenticate_user = $user;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new FakeEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'admin', 'password' => 'adm1n']);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
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

    public function testLoginEndpointWithInvalidCredentials(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->authenticate_with_error = new InvalidCredentialsException('test');
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'wrooong', 'password' => 'wrooong']);

        $this->assertSame([
            'status' => 'INVALID_CREDENTIALS',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testLoginEndpointCanNotAuthenticate(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->authenticate_with_error = new AuthBlockedException('test');
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'admin', 'password' => 'adm1n']);

        $this->assertSame([
            'status' => 'BLOCKED',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }
}
