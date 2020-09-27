<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUserPasswordEndpoint.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeUserPasswordEndpointEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'User' => new FakeUserPasswordEndpointUserRepository(),
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

class FakeUserPasswordEndpointUserRepository {
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
 * @coversNothing
 */
final class UpdateUserPasswordEndpointTest extends TestCase {
    public function testUpdateUserPasswordEndpoint(): void {
        $entity_manager = new FakeUserPasswordEndpointEndpointEntityManager();
        $endpoint = new UpdateUserPasswordEndpoint($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call([
            'id' => 1,
            'oldPassword' => 'adm1n',
            'newPassword' => '1234',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(1, $admin_user->getId());
        $this->assertTrue(password_verify('1234', $admin_user->getPasswordHash()));
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
    }
}
