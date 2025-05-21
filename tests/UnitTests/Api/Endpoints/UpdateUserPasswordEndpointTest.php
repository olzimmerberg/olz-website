<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\UpdateUserPasswordEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\UpdateUserPasswordEndpoint
 */
final class UpdateUserPasswordEndpointTest extends UnitTestCase {
    public function testUpdateUserPasswordEndpointShortPassword(): void {
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        try {
            $endpoint->call([
                'id' => 1,
                'oldPassword' => 'adm1n',
                'newPassword' => '1234',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'NOTICE Bad user request',
            ], $this->getLogs());
            $this->assertSame([
                'newPassword' => ['Das neue Passwort muss mindestens 8 Zeichen lang sein.'],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testUpdateUserPasswordEndpointWrongUser(): void {
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'not_admin',
        ];

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'adm1n',
            'newPassword' => '12345678',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OTHER_USER'], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame(md5('adm1n'), $admin_user->getPasswordHash()); // just for test
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'not_admin',
        ], WithUtilsCache::get('session')->session_storage);
    }

    public function testUpdateUserPasswordEndpointWrongOldPassword(): void {
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'incorrect_password',
            'newPassword' => '12345678',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_OLD'], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame(md5('adm1n'), $admin_user->getPasswordHash()); // just for test
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], WithUtilsCache::get('session')->session_storage);
    }

    public function testUpdateUserPasswordEndpoint(): void {
        $endpoint = new UpdateUserPasswordEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        $result = $endpoint->call([
            'id' => 2,
            'oldPassword' => 'adm1n',
            'newPassword' => '12345678',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        // Just for test; no need for security (salt, etc.).
        $this->assertSame(md5('12345678'), $admin_user->getPasswordHash()); // just for test
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], WithUtilsCache::get('session')->session_storage);
    }
}
