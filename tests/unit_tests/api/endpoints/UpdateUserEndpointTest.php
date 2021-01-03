<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUserEndpoint.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeUserEndpointEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'User' => new FakeUserEndpointUserRepository(),
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

class FakeUserEndpointUserRepository {
    public function __construct() {
        $admin_user = get_fake_user();
        $admin_user->setId(1);
        $admin_user->setUsername('admin');
        $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
        $admin_user->setZugriff('ftp');
        $admin_user->setRoot('karten');
        $this->admin_user = $admin_user;
    }

    public function findOneBy($where) {
        if ($where === ['id' => 1]) {
            return $this->admin_user;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \UpdateUserEndpoint
 */
final class UpdateUserEndpointTest extends TestCase {
    public function testUpdateUserEndpoint(): void {
        $entity_manager = new FakeUserEndpointEndpointEntityManager();
        $endpoint = new UpdateUserEndpoint($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call([
            'id' => 1,
            'firstName' => 'First',
            'lastName' => 'Last',
            'username' => 'test',
            'email' => 'test@olzimmerberg.ch',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(1, $admin_user->getId());
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('test@olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'test',
        ], $session->session_storage);
    }
}
