<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../../src/api/endpoints/LoginEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeLoginEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'AuthRequest' => new FakeLoginEndpointAuthRequestRepository(),
            'User' => new FakeLoginEndpointUserRepository(),
        ];
    }

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
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
            $admin_user->setUsername('admin');
            $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
            $admin_user->setZugriff('ftp');
            $admin_user->setRoot('karten');
            return $admin_user;
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
        $logger = new Logger('LoginEndpointTest');
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'username' => ['Feld darf nicht leer sein.'],
                'password' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testLoginEndpointWithCorrectCredentials(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = new Logger('LoginEndpointTest');
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['username' => 'admin', 'password' => 'adm1n']);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
        ], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }

    public function testLoginEndpointWithWrongUsername(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = new Logger('LoginEndpointTest');
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['username' => 'wrooong', 'password' => 'adm1n']);

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
    }

    public function testLoginEndpointWithWrongPassword(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = new Logger('LoginEndpointTest');
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['username' => 'admin', 'password' => 'wrooong']);

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
    }

    public function testLoginEndpointCanNotAuthenticate(): void {
        $entity_manager = new FakeLoginEndpointEntityManager();
        $logger = new Logger('LoginEndpointTest');
        $endpoint = new LoginEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $entity_manager->getRepository('AuthRequest')->can_authenticate = false;
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['username' => 'admin', 'password' => 'adm1n']);

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
    }
}
