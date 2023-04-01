<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\MemorySession;

class FakeAuthUtilsAccessTokenRepository {
    public function findOneBy($where) {
        if ($where === ['token' => 'valid-token-1']) {
            $token = new AccessToken();
            $token->setId(1);
            $token->setToken('valid-token-1');
            $token->setUser(Fake\FakeUsers::adminUser());
            $token->setExpiresAt(new \DateTime('2022-01-24 00:00:00'));
            return $token;
        }
        if ($where === ['token' => 'expired-token-1']) {
            $token = new AccessToken();
            $token->setId(2);
            $token->setToken('expired-token-1');
            $token->setUser(Fake\FakeUsers::adminUser());
            $token->setExpiresAt(new \DateTime('2020-01-11 20:00:00'));
            return $token;
        }
        return null;
    }
}

class FakeAuthUtilsAuthRequestRepository {
    public $auth_requests = [];
    public $can_authenticate = true;
    public $can_validate_access_token = true;

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
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        // Also test that it's resolving admin-old to admin
        $result = $auth_utils->authenticate('admin-old', 'adm1n');

        $this->assertNotSame(null, Fake\FakeUsers::adminUser());
        $this->assertSame(Fake\FakeUsers::adminUser(), $result);
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
            "INFO   Auth: all",
            "INFO   Root: karten",
        ], $logger->handler->getPrettyRecords());
    }

    public function testAuthenticateWithWrongUsername(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testAuthenticateWithWrongPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testAuthenticateCanNotAuthenticate(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $auth_request_repo->can_authenticate = false;
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testValidateValidAccessToken(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        $result = $auth_utils->validateAccessToken('valid-token-1');

        $this->assertSame(Fake\FakeUsers::adminUser(), $result);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testValidateInvalidAccessToken(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testValidateExpiredAccessToken(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testValidateAccessTokenCanNotValidate(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $auth_request_repo->can_validate_access_token = false;
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLog($logger);
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
        ], $logger->handler->getPrettyRecords());
    }

    public function testResolveUsername(): void {
        $entity_manager = new Fake\FakeEntityManager();

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);

        $result = $auth_utils->resolveUsernameOrEmail('admin');
        $this->assertSame(Fake\FakeUsers::adminUser(), $result);
    }

    public function testResolveOldUsername(): void {
        $entity_manager = new Fake\FakeEntityManager();

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);

        $result = $auth_utils->resolveUsernameOrEmail('admin-old');
        $this->assertSame(Fake\FakeUsers::adminUser(), $result);
    }

    public function testResolveEmail(): void {
        $entity_manager = new Fake\FakeEntityManager();

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);

        $result = $auth_utils->resolveUsernameOrEmail('vorstand@test.olzimmerberg.ch');
        $this->assertSame(Fake\FakeUsers::vorstandUser(), $result);
    }

    public function testResolveUsernameEmail(): void {
        $entity_manager = new Fake\FakeEntityManager();

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);

        $result = $auth_utils->resolveUsernameOrEmail('admin@olzimmerberg.ch');
        $this->assertSame(Fake\FakeUsers::adminUser(), $result);
    }

    public function testResolveOldUsernameEmail(): void {
        $entity_manager = new Fake\FakeEntityManager();

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);

        $result = $auth_utils->resolveUsernameOrEmail('admin-old@olzimmerberg.ch');
        $this->assertSame(Fake\FakeUsers::adminUser(), $result);
    }

    public function testHasPermissionNoUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->hasPermission('test'));
        $this->assertSame(false, $auth_utils->hasPermission('other'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(false, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithNoPermission(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'no',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->hasPermission('test'));
        $this->assertSame(false, $auth_utils->hasPermission('other'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithSpecificPermission(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'specific',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasPermission('test'));
        $this->assertSame(false, $auth_utils->hasPermission('other'));
        $this->assertSame(false, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithAllPermissions(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(true, $auth_utils->hasPermission('test'));
        $this->assertSame(true, $auth_utils->hasPermission('other'));
        $this->assertSame(true, $auth_utils->hasPermission('all'));
        $this->assertSame(true, $auth_utils->hasPermission('any'));
    }

    public function testHasRolePermissionNoRole(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(false, $auth_utils->hasRolePermission('test', null));
        $this->assertSame(false, $auth_utils->hasRolePermission('other', null));
        $this->assertSame(false, $auth_utils->hasRolePermission('all', null));
        $this->assertSame(false, $auth_utils->hasRolePermission('any', null));
    }

    public function testHasRolePermissionWithNoPermission(): void {
        $role = Fake\FakeRoles::defaultRole();
        $role->setPermissions('');
        $auth_utils = new AuthUtils();
        $this->assertSame(false, $auth_utils->hasRolePermission('test', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('other', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('all', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('any', $role));
    }

    public function testHasRolePermissionWithSpecificPermission(): void {
        $role = Fake\FakeRoles::defaultRole();
        $role->setPermissions('test');
        $auth_utils = new AuthUtils();
        $this->assertSame(true, $auth_utils->hasRolePermission('test', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('other', $role));
        $this->assertSame(false, $auth_utils->hasRolePermission('all', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('any', $role));
    }

    public function testHasRolePermissionWithAllPermissions(): void {
        $role = Fake\FakeRoles::defaultRole();
        $role->setPermissions('all');
        $auth_utils = new AuthUtils();
        $this->assertSame(true, $auth_utils->hasRolePermission('test', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('other', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('all', $role));
        $this->assertSame(true, $auth_utils->hasRolePermission('any', $role));
    }

    public function testGetCurrentUserFromToken(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams(['access_token' => 'valid-token-1']);
        $auth_utils->setLog($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(Fake\FakeUsers::adminUser(), $auth_utils->getCurrentUser());
    }

    public function testGetCurrentUserFromSession(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(Fake\FakeUsers::adminUser(), $auth_utils->getCurrentUser());
    }

    public function testGetTokenUser(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams(['access_token' => 'valid-token-1']);
        $auth_utils->setLog($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(Fake\FakeUsers::adminUser(), $auth_utils->getTokenUser());
    }

    public function testGetTokenUserForInvalidToken(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams(['access_token' => 'invalid-token']);
        $auth_utils->setLog($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(null, $auth_utils->getTokenUser());
    }

    public function testGetSessionUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(Fake\FakeUsers::vorstandUser(), $auth_utils->getSessionUser());
    }

    public function testGetCurrentAuthUserFromSession(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'auth_user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(Fake\FakeUsers::adminUser(), $auth_utils->getCurrentAuthUser());
    }

    public function testGetSessionAuthUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'auth_user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(Fake\FakeUsers::vorstandUser(), $auth_utils->getSessionAuthUser());
    }

    public function testGetAuthenticatedRoles(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(['admin_role'], array_map(function ($role) {
            return $role->getUsername();
        }, $auth_utils->getAuthenticatedRoles()));
    }

    public function testGetAuthenticatedRolesUnauthenticated(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(null, $auth_utils->getAuthenticatedRoles());
    }

    public function testIsRoleIdAuthenticated(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(1));
        $this->assertSame(true, $auth_utils->isRoleIdAuthenticated(2));
        $this->assertSame(false, $auth_utils->isRoleIdAuthenticated(3));
    }

    public function testIsRoleIdAuthenticatedUnauthenticated(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
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
        $env_utils = new Fake\FakeEnvUtils();
        $auth_utils = new AuthUtils();
        $auth_utils->setEnvUtils($env_utils);
        $this->assertSame(
            '/_/icns/user.php?initials=%3F',
            $auth_utils->getUserAvatar(null)
        );
    }

    public function testGetUserAvatarHasAvatar(): void {
        $env_utils = new Fake\FakeEnvUtils();
        $auth_utils = new AuthUtils();
        $auth_utils->setEnvUtils($env_utils);
        $user = Fake\FakeUsers::adminUser();

        $data_path = $env_utils->getDataPath();
        $user_image_path = "{$data_path}img/users/{$user->getId()}.jpg";
        mkdir(dirname($user_image_path), 0777, true);
        file_put_contents($user_image_path, '');

        $this->assertSame(
            "/data-href/img/users/{$user->getId()}.jpg",
            $auth_utils->getUserAvatar($user)
        );
    }

    public function testGetUserAvatarNoAvatar(): void {
        $env_utils = new Fake\FakeEnvUtils();
        $auth_utils = new AuthUtils();
        $auth_utils->setEnvUtils($env_utils);
        $user = Fake\FakeUsers::adminUser();
        $this->assertSame(
            '/_/icns/user.php?initials=AI',
            $auth_utils->getUserAvatar($user)
        );
    }
}
