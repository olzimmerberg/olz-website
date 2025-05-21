<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LoginEndpoint;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LoginEndpoint
 */
final class LoginEndpointTest extends UnitTestCase {
    public function testLoginEndpointWithoutInput(): void {
        $endpoint = new LoginEndpoint();
        $endpoint->runtimeSetup();
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => ["Fehlender Schlüssel: usernameOrEmail."],
                'password' => ["Fehlender Schlüssel: password."],
                'rememberMe' => ["Fehlender Schlüssel: rememberMe."],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testLoginEndpointWithNullInput(): void {
        $endpoint = new LoginEndpoint();
        $endpoint->runtimeSetup();
        try {
            $result = $endpoint->call([
                'usernameOrEmail' => null,
                'password' => null,
                'rememberMe' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                'password' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                'rememberMe' => [['.' => ['Wert muss vom Typ bool sein.']]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testLoginEndpointWithCorrectCredentials(): void {
        $user = FakeUser::adminUser();
        WithUtilsCache::get('authUtils')->authenticate_user = $user;
        $entity_manager = WithUtilsCache::get('entityManager');
        $endpoint = new LoginEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'password' => 'adm1n',
            'rememberMe' => true,
        ]);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
            'numRemainingAttempts' => null,
        ], $result);
        $this->assertSame([
            'auth' => 'all verified_email',
            'root' => 'karten',
            'user' => 'admin',
            'user_id' => '2',
            'auth_user' => 'admin',
            'auth_user_id' => '2',
        ], WithUtilsCache::get('session')->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastLoginAt()?->format('Y-m-d H:i:s')
        );
        $this->assertTrue($entity_manager->flushed);
    }

    public function testLoginEndpointWithInvalidCredentials(): void {
        WithUtilsCache::get('authUtils')->authenticate_with_error = new InvalidCredentialsException('test', 3);
        $endpoint = new LoginEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'usernameOrEmail' => 'wrooong',
            'password' => 'wrooong',
            'rememberMe' => false,
        ]);

        $this->assertSame([
            'status' => 'INVALID_CREDENTIALS',
            'numRemainingAttempts' => 3,
        ], $result);
        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testLoginEndpointCanNotAuthenticate(): void {
        WithUtilsCache::get('authUtils')->authenticate_with_error = new AuthBlockedException('test');
        $endpoint = new LoginEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'password' => 'adm1n',
            'rememberMe' => false,
        ]);

        $this->assertSame([
            'status' => 'BLOCKED',
            'numRemainingAttempts' => 0,
        ], $result);
        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
    }
}
