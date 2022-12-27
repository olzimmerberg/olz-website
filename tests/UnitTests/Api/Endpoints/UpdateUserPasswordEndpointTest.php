<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Monolog\Logger;
use Olz\Api\Endpoints\UpdateUserPasswordEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\UpdateUserPasswordEndpoint
 */
final class UpdateUserPasswordEndpointTest extends UnitTestCase {
    public function testUpdateUserPasswordEndpointIdent(): void {
        $endpoint = new UpdateUserPasswordEndpoint();
        $this->assertSame('UpdateUserPasswordEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserPasswordEndpointShortPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new Fake\FakeAuthUtils();
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
        $endpoint->setLog($logger);

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
        $entity_manager = new Fake\FakeEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new Fake\FakeAuthUtils();
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
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'adm1n',
            'newPassword' => '12345678',
        ]);

        $this->assertSame(['status' => 'OTHER_USER'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertTrue(password_verify('adm1n', $admin_user->getPasswordHash()));
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'not_admin',
        ], $session->session_storage);
    }

    public function testUpdateUserPasswordEndpointWrongOldPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new Fake\FakeAuthUtils();
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
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'incorrect_password',
            'newPassword' => '12345678',
        ]);

        $this->assertSame(['status' => 'INVALID_OLD'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertTrue(password_verify('adm1n', $admin_user->getPasswordHash()));
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
    }

    public function testUpdateUserPasswordEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = new Logger('UpdateUserPasswordEndpointTest');
        $auth_utils = new Fake\FakeAuthUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'adm1n',
            'newPassword' => '12345678',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertTrue(password_verify('12345678', $admin_user->getPasswordHash()));
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
    }
}
