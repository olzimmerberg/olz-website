<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUserPasswordEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeUserPasswordEndpointEndpointEntityManager extends FakeEntityManager {
    public function __construct() {
        $this->repositories = [
            'User' => new FakeUserRepository(),
        ];
    }
}

class FakeUserPasswordEndpointAuthUtils {
    public function isPasswordAllowed($password) {
        return strlen($password) >= 8;
    }
}

/**
 * @internal
 * @covers \UpdateUserPasswordEndpoint
 */
final class UpdateUserPasswordEndpointTest extends UnitTestCase {
    public function testUpdateUserPasswordEndpointIdent(): void {
        $endpoint = new UpdateUserPasswordEndpoint();
        $this->assertSame('UpdateUserPasswordEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserPasswordEndpointShortPassword(): void {
        $entity_manager = new FakeUserPasswordEndpointEndpointEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new FakeUserPasswordEndpointAuthUtils();
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call([
                'id' => 1,
                'oldPassword' => 'adm1n',
                'newPassword' => '1234',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'newPassword' => ['Das neue Passwort muss mindestens 8 Zeichen lang sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testUpdateUserPasswordEndpointWrongUser(): void {
        $entity_manager = new FakeUserPasswordEndpointEndpointEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new FakeUserPasswordEndpointAuthUtils();
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'not_admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'adm1n',
            'newPassword' => '12345678',
        ]);

        $this->assertSame(['status' => 'OTHER_USER'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertTrue(password_verify('adm1n', $admin_user->getPasswordHash()));
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'not_admin',
        ], $session->session_storage);
    }

    public function testUpdateUserPasswordEndpointWrongOldPassword(): void {
        $entity_manager = new FakeUserPasswordEndpointEndpointEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new FakeUserPasswordEndpointAuthUtils();
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
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
            'id' => 2,
            'oldPassword' => 'incorrect_password',
            'newPassword' => '12345678',
        ]);

        $this->assertSame(['status' => 'INVALID_OLD'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertTrue(password_verify('adm1n', $admin_user->getPasswordHash()));
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
    }

    public function testUpdateUserPasswordEndpoint(): void {
        $entity_manager = new FakeUserPasswordEndpointEndpointEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new FakeUserPasswordEndpointAuthUtils();
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
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
            'id' => 2,
            'oldPassword' => 'adm1n',
            'newPassword' => '12345678',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertTrue(password_verify('12345678', $admin_user->getPasswordHash()));
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
    }
}
