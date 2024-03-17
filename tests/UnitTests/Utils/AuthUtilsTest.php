<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\Entity\Roles\FakeRoles;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\MemorySession;
use Olz\Utils\WithUtilsCache;

class FakeAuthUtilsAccessTokenRepository {
    public function findOneBy($where) {
        if ($where === ['token' => 'valid-token-1']) {
            $token = new AccessToken();
            $token->setId(1);
            $token->setToken('valid-token-1');
            $token->setUser(FakeUser::adminUser());
            $token->setExpiresAt(new \DateTime('2022-01-24 00:00:00'));
            return $token;
        }
        if ($where === ['token' => 'expired-token-1']) {
            $token = new AccessToken();
            $token->setId(2);
            $token->setToken('expired-token-1');
            $token->setUser(FakeUser::adminUser());
            $token->setExpiresAt(new \DateTime('2020-01-11 20:00:00'));
            return $token;
        }
        return null;
    }
}

class FakeAuthUtilsAuthRequestRepository {
    public $auth_requests = [];
    public $num_remaining_attempts = 3;
    public $can_validate_access_token = true;

    public function addAuthRequest($ip_address, $action, $username, $timestamp = null) {
        $this->auth_requests[] = [
            'ip_address' => $ip_address,
            'action' => $action,
            'timestamp' => $timestamp,
            'username' => $username,
        ];
    }

    public function numRemainingAttempts($ip_address, $timestamp = null) {
        return $this->num_remaining_attempts;
    }

    public function canValidateAccessToken($ip_address, $timestamp = null) {
        return $this->can_validate_access_token;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthUtils
 */
final class AuthUtilsTest extends UnitTestCase {
    public function testAuthenticateWithCorrectCredentials(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        // Also test that it's resolving admin-old to admin
        $result = $auth_utils->authenticate('admin-old', 'adm1n');

        $this->assertNotSame(null, FakeUser::adminUser());
        $this->assertSame(FakeUser::adminUser(), $result);
        $this->assertSame([
            'user' => 'inexistent', // for now, we don't modify the session
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'admin-old',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "INFO User login successful: admin-old",
            "INFO   Auth: all verified_email",
            "INFO   Root: karten",
        ], $this->getLogs());
    }

    public function testAuthenticateWithWrongUsername(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        try {
            $auth_utils->authenticate('wrooong', 'adm1n');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Login attempt with invalid credentials from IP: 1.2.3.4 (user: wrooong).',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_CREDENTIALS',
                'timestamp' => null,
                'username' => 'wrooong',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "NOTICE Login attempt with invalid credentials from IP: 1.2.3.4 (user: wrooong).",
        ], $this->getLogs());
    }

    public function testAuthenticateWithWrongPassword(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        try {
            $auth_utils->authenticate('admin', 'wrooong');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Login attempt with invalid credentials from IP: 1.2.3.4 (user: admin).',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_CREDENTIALS',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "NOTICE Login attempt with invalid credentials from IP: 1.2.3.4 (user: admin).",
        ], $this->getLogs());
    }

    public function testAuthenticateCanNotAuthenticate(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $auth_request_repo->num_remaining_attempts = 0;
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        try {
            $auth_utils->authenticate('admin', 'adm1n');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Login attempt from blocked IP: 1.2.3.4 (user: admin).',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'BLOCKED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "NOTICE Login attempt from blocked IP: 1.2.3.4 (user: admin).",
        ], $this->getLogs());
    }

    public function testValidateValidAccessToken(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        $result = $auth_utils->validateAccessToken('valid-token-1');

        $this->assertSame(FakeUser::adminUser(), $result);
        $this->assertSame([
            'user' => 'inexistent', // for now, we don't modify the session
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'TOKEN_VALIDATED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "INFO Token validation successful: 1",
        ], $this->getLogs());
    }

    public function testValidateInvalidAccessToken(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        try {
            $auth_utils->validateAccessToken('invalid-token');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Invalid access token validation from IP: 1.2.3.4.',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_TOKEN',
                'timestamp' => null,
                'username' => '',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "NOTICE Invalid access token validation from IP: 1.2.3.4.",
        ], $this->getLogs());
    }

    public function testValidateExpiredAccessToken(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        try {
            $auth_utils->validateAccessToken('expired-token-1');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Expired access token validation from IP: 1.2.3.4.',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'EXPIRED_TOKEN',
                'timestamp' => null,
                'username' => '',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "NOTICE Expired access token validation from IP: 1.2.3.4.",
        ], $this->getLogs());
    }

    public function testValidateAccessTokenCanNotValidate(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $auth_request_repo->can_validate_access_token = false;
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        try {
            $auth_utils->validateAccessToken('valid-token-1');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Access token validation from blocked IP: 1.2.3.4.',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'TOKEN_BLOCKED',
                'timestamp' => null,
                'username' => '',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "NOTICE Access token validation from blocked IP: 1.2.3.4.",
        ], $this->getLogs());
    }

    public function testResolveUsername(): void {
        $entity_manager = WithUtilsCache::get('entityManager');

        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testResolveOldUsername(): void {
        $entity_manager = WithUtilsCache::get('entityManager');

        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin-old');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testResolveEmail(): void {
        $entity_manager = WithUtilsCache::get('entityManager');

        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('vorstand@olzimmerberg.ch');
        $this->assertSame(FakeUser::vorstandUser(), $result);
    }

    public function testResolveUsernameEmail(): void {
        $entity_manager = WithUtilsCache::get('entityManager');

        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin@olzimmerberg.ch');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testResolveOldUsernameEmail(): void {
        $entity_manager = WithUtilsCache::get('entityManager');

        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin-old@olzimmerberg.ch');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testHasPermissionNoUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->hasPermission('test'));
        $this->assertSame(false, $auth_utils->hasPermission('other'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(false, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithNoPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'no',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->hasPermission('test'));
        $this->assertSame(false, $auth_utils->hasPermission('other'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithSpecificPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'specific',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasPermission('test'));
        $this->assertSame(false, $auth_utils->hasPermission('other'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithAllPermissions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasPermission('test'));
        $this->assertSame(true, $auth_utils->hasPermission('other'));
        $this->assertSame(true, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithRolePermissions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasPermission('vorstand_user'));
        $this->assertSame(true, $auth_utils->hasPermission('vorstand_role'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasUserPermissionNoUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->hasUserPermission('test'));
        $this->assertSame(false, $auth_utils->hasUserPermission('other'));
        $this->assertSame(false, $auth_utils->hasUserPermission('all'));
        $this->assertSame(false, $auth_utils->hasUserPermission('any'));
    }

    public function testHasUserPermissionWithNoPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'no',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->hasUserPermission('test'));
        $this->assertSame(false, $auth_utils->hasUserPermission('other'));
        $this->assertSame(false, $auth_utils->hasUserPermission('all'));
        $this->assertSame(true, $auth_utils->hasUserPermission('any'));
    }

    public function testHasUserPermissionWithSpecificPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'specific',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasUserPermission('test'));
        $this->assertSame(false, $auth_utils->hasUserPermission('other'));
        $this->assertSame(false, $auth_utils->hasUserPermission('all'));
        $this->assertSame(true, $auth_utils->hasUserPermission('any'));
    }

    public function testHasUserPermissionWithAllPermissions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasUserPermission('test'));
        $this->assertSame(true, $auth_utils->hasUserPermission('other'));
        $this->assertSame(true, $auth_utils->hasUserPermission('all'));
        $this->assertSame(true, $auth_utils->hasUserPermission('any'));
    }

    public function testHasUserPermissionWithRolePermissions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasUserPermission('vorstand_user'));
        $this->assertSame(false, $auth_utils->hasUserPermission('vorstand_role'));
        $this->assertSame(false, $auth_utils->hasUserPermission('all'));
        $this->assertSame(true, $auth_utils->hasUserPermission('any'));
    }

    public function testHasRolePermissionNoRole(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(false, $auth_utils->hasRolePermission('test', null));
        $this->assertSame(false, $auth_utils->hasRolePermission('other', null));
        $this->assertSame(false, $auth_utils->hasRolePermission('all', null));
        $this->assertSame(false, $auth_utils->hasRolePermission('any', null));
    }

    public function testHasRolePermissionWithNoPermission(): void {
        $role = FakeRoles::defaultRole();
        $role->setPermissions('');
        $auth_utils = new AuthUtils();
        $this->assertSame(false, $auth_utils->hasRolePermission('test', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('other', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('all', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('any', $role));
    }

    public function testHasRolePermissionWithSpecificPermission(): void {
        $role = FakeRoles::defaultRole();
        $role->setPermissions('test');
        $auth_utils = new AuthUtils();
        $this->assertSame(true, $auth_utils->hasRolePermission('test', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('other', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('all', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('any', $role));
    }

    public function testHasRolePermissionWithAllPermissions(): void {
        $role = FakeRoles::defaultRole();
        $role->setPermissions('all');
        $auth_utils = new AuthUtils();
        $this->assertSame(true, $auth_utils->hasRolePermission('test', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('other', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('all', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('any', $role));
    }

    public function testGetCurrentUserFromToken(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams(['access_token' => 'valid-token-1']);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getCurrentUser());
    }

    public function testGetCurrentUserFromSession(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getCurrentUser());
    }

    public function testGetTokenUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams(['access_token' => 'valid-token-1']);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getTokenUser());
    }

    public function testGetTokenUserForInvalidToken(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams(['access_token' => 'invalid-token']);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(null, $auth_utils->getTokenUser());
    }

    public function testGetSessionUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(FakeUser::vorstandUser(), $auth_utils->getSessionUser());
    }

    public function testGetCurrentAuthUserFromSession(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'auth_user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getCurrentAuthUser());
    }

    public function testGetSessionAuthUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'auth_user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setSession($session);
        $this->assertSame(FakeUser::vorstandUser(), $auth_utils->getSessionAuthUser());
    }

    public function testGetAuthenticatedRoles(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(['admin_role'], array_map(function ($role) {
            return $role->getUsername();
        }, $auth_utils->getAuthenticatedRoles()));
    }

    public function testGetAuthenticatedRolesUnauthenticated(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(null, $auth_utils->getAuthenticatedRoles());
    }

    public function testIsRoleIdAuthenticated(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(1));
        $this->assertSame(true, $auth_utils->isRoleIdAuthenticated(2));
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(3));
    }

    public function testIsRoleIdAuthenticatedUnauthenticated(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $session = new MemorySession();
        $session->session_storage = [];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(1));
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(2));
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(3));
    }

    public function testIsUsernameAllowed(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(true, $auth_utils->isUsernameAllowed('testTEST1234.-_'));
        $this->assertSame(false, $auth_utils->isUsernameAllowed('test@wtf'));
        $this->assertSame(false, $auth_utils->isUsernameAllowed('ötzi'));
        $this->assertSame(false, $auth_utils->isUsernameAllowed('\';DROP TABLE users;'));
    }

    public function testIsPasswordAllowed(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(false, $auth_utils->isPasswordAllowed('test'));
        $this->assertSame(true, $auth_utils->isPasswordAllowed('longpassword'));
        $this->assertSame(false, $auth_utils->isPasswordAllowed('1234567'));
        $this->assertSame(true, $auth_utils->isPasswordAllowed('12345678'));
    }

    public function testGetUserAvatarNoUser(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(
            ['1x' => '/_/assets/user_initials_%3F.svg'],
            $auth_utils->getUserAvatar(null)
        );
    }

    public function testGetUserAvatarHasAvatar(): void {
        $auth_utils = new AuthUtils();
        $user = FakeUser::adminUser();

        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $user_image_path = "{$data_path}img/users/{$user->getId()}.jpg";
        mkdir(dirname($user_image_path), 0777, true);
        file_put_contents($user_image_path, '');

        $this->assertSame(
            ['1x' => "/data-href/img/users/{$user->getId()}.jpg"],
            $auth_utils->getUserAvatar($user)
        );
    }

    public function testGetUserAvatarHasHighResolutionAvatar(): void {
        $auth_utils = new AuthUtils();
        $user = FakeUser::adminUser();

        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $user_image_path = "{$data_path}img/users/{$user->getId()}.jpg";
        $user_image_2x_path = "{$data_path}img/users/{$user->getId()}@2x.jpg";
        mkdir(dirname($user_image_path), 0777, true);
        file_put_contents($user_image_path, '');
        file_put_contents($user_image_2x_path, '');

        $this->assertSame(
            [
                '2x' => "/data-href/img/users/{$user->getId()}@2x.jpg",
                '1x' => "/data-href/img/users/{$user->getId()}.jpg",
            ],
            $auth_utils->getUserAvatar($user)
        );
    }

    public function testGetUserAvatarNoAvatar(): void {
        $auth_utils = new AuthUtils();
        $user = FakeUser::adminUser();
        $this->assertSame(
            ['1x' => '/_/assets/user_initials_AI.svg'],
            $auth_utils->getUserAvatar($user)
        );
    }

    public function testGetUserAvatarNoAvatarSpecialChars(): void {
        $auth_utils = new AuthUtils();
        $user = FakeUser::adminUser();
        $user->setFirstName("Özdemir");
        $user->setLastName(null);
        $this->assertSame(
            ['1x' => '/_/assets/user_initials_%C3%96%3F.svg'],
            $auth_utils->getUserAvatar($user)
        );
    }
}
