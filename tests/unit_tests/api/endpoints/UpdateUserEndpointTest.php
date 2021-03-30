<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUserEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeUpdateUserEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'User' => new FakeUpdateUserEndpointUserRepository(),
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

class FakeUpdateUserEndpointUserRepository {
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
final class UpdateUserEndpointTest extends UnitTestCase {
    public function testUpdateUserEndpointIdent(): void {
        $endpoint = new UpdateUserEndpoint();
        $this->assertSame('UpdateUserEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserEndpoint(): void {
        $entity_manager = new FakeUpdateUserEndpointEntityManager();
        $logger = new Logger('UpdateUserEndpointTest');
        $endpoint = new UpdateUserEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 1,
            'firstName' => 'First',
            'lastName' => 'Last',
            'username' => 'test',
            'email' => 'test@olzimmerberg.ch',
            'gender' => 'F',
            'birthdate' => '1992-08-05 12:00:00',
            'street' => 'Teststrasse 123',
            'postalCode' => '1234',
            'city' => 'Muster',
            'region' => 'ZH',
            'countryCode' => 'CH',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(1, $admin_user->getId());
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('test@olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame('F', $admin_user->getGender());
        $this->assertSame('1992-08-05 12:00:00', $admin_user->getBirthdate()->format('Y-m-d H:i:s'));
        $this->assertSame('Teststrasse 123', $admin_user->getStreet());
        $this->assertSame('1234', $admin_user->getPostalCode());
        $this->assertSame('Muster', $admin_user->getCity());
        $this->assertSame('ZH', $admin_user->getRegion());
        $this->assertSame('CH', $admin_user->getCountryCode());
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'test',
        ], $session->session_storage);
    }
}
