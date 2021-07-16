<?php

declare(strict_types=1);

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../../src/api/endpoints/LoginEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeLoginEndpointEntityManager extends FakeEntityManager {
    public function __construct() {
        $this->repositories = [
            'AuthRequest' => new FakeLoginEndpointAuthRequestRepository(),
            'User' => new FakeLoginEndpointUserRepository(),
        ];
    }
}

class FakeLoginEndpointAuthRequestRepository {
    public $auth_requests = [];
    public $can_authenticate = true;

    public function addAuthRequest($ip_address, $action, $username, $timestamp = null) {
        $this->auth_requests[] = [
            'ip_address' => $ip_address,
            'action' => $action,
            'timestamp' => $timestamp,
            'username' => $username,
        ];
    }

    public function canAuthenticate($ip_address, $timestamp = null) {
        return $this->can_authenticate;
    }
}

class FakeLoginEndpointUserRepository {
    public function findOneBy($where) {
        if ($where === ['username' => 'admin']) {
            $admin_user = get_fake_user();
            $admin_user->setId(2);
            $admin_user->setUsername('admin');
            $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
            $admin_user->setZugriff('ftp');
            $admin_user->setRoot('karten');
            return $admin_user;
        }
        if ($where === ['email' => 'vorstand@test.olzimmerberg.ch']) {
            $vorstand_user = get_fake_user();
            $vorstand_user->setId(3);
            $vorstand_user->setUsername('vorstand');
            $vorstand_user->setPasswordHash(password_hash('v0r57and', PASSWORD_DEFAULT));
            $vorstand_user->setZugriff('aktuell ftp');
            $vorstand_user->setRoot('vorstand');
            return $vorstand_user;
        }
        return null;
    }
}

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
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => ['Feld darf nicht leer sein.'],
                'password' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testLoginEndpointWithCorrectCredentials(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'admin', 'password' => 'adm1n']);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
        ], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
            'user_id' => 2,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
        $this->assertSame([
            "INFO Valid user request",
            "INFO User logged in: admin",
            "INFO   Auth: ftp",
            "INFO   Root: karten",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testLoginEndpointWithCorrectEmailCredentials(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'vorstand@test.olzimmerberg.ch',
            'password' => 'v0r57and',
        ]);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
        ], $result);
        $this->assertSame([
            'auth' => 'aktuell ftp',
            'root' => 'vorstand',
            'user' => 'vorstand',
            'user_id' => 3,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'vorstand@test.olzimmerberg.ch',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
        $this->assertSame([
            "INFO Valid user request",
            "INFO User logged in: vorstand@test.olzimmerberg.ch",
            "INFO   Auth: aktuell ftp",
            "INFO   Root: vorstand",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testLoginEndpointWithWrongUsername(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'wrooong', 'password' => 'adm1n']);

        $this->assertSame([
            'status' => 'INVALID_CREDENTIALS',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_CREDENTIALS',
                'timestamp' => null,
                'username' => 'wrooong',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Login attempt with invalid credentials from user: wrooong (1.2.3.4).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testLoginEndpointWithWrongPassword(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'admin', 'password' => 'wrooong']);

        $this->assertSame([
            'status' => 'INVALID_CREDENTIALS',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_CREDENTIALS',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Login attempt with invalid credentials from user: admin (1.2.3.4).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testLoginEndpointCanNotAuthenticate(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = FakeLogger::create();
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $entity_manager->getRepository('AuthRequest')->can_authenticate = false;
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['usernameOrEmail' => 'admin', 'password' => 'adm1n']);

        $this->assertSame([
            'status' => 'BLOCKED',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'BLOCKED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Login attempt from blocked user: admin (1.2.3.4).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }
}
