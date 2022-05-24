<?php

declare(strict_types=1);

use App\Entity\AccessToken;
use App\Entity\AuthRequest;

require_once __DIR__.'/../../../../_/utils/auth/AuthUtils.php';
require_once __DIR__.'/../../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../../_/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeAuthUtilsAccessTokenRepository {
    public function findOneBy($where) {
        if ($where === ['token' => 'valid-token-1']) {
            $token = new AccessToken();
            $token->setId(1);
            $token->setToken('valid-token-1');
            $token->setUser(FakeUsers::adminUser());
            $token->setExpiresAt(new \DateTime('2022-01-24 00:00:00'));
            return $token;
        }
        if ($where === ['token' => 'expired-token-1']) {
            $token = new AccessToken();
            $token->setId(2);
            $token->setToken('expired-token-1');
            $token->setUser(FakeUsers::adminUser());
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
 * @covers \AuthUtils
 */
final class AuthUtilsTest extends UnitTestCase {
    public function testAuthenticateWithCorrectCredentials(): void {
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        $result = $auth_utils->authenticate('admin', 'adm1n');

        $this->assertNotSame(null, FakeUsers::adminUser());
        $this->assertSame(FakeUsers::adminUser(), $result);
        $this->assertSame([
            'user' => 'inexistent', // for now, we don't modify the session
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "INFO User login successful: admin",
            "INFO   Auth: all",
            "INFO   Root: karten",
        ], $logger->handler->getPrettyRecords());
    }

    public function testAuthenticateWithCorrectEmailCredentials(): void {
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        $result = $auth_utils->authenticate('vorstand@test.olzimmerberg.ch', 'v0r57and');

        $this->assertNotSame(null, FakeUsers::vorstandUser());
        $this->assertSame(FakeUsers::vorstandUser(), $result);
        $this->assertSame([
            'user' => 'inexistent', // for now, we don't modify the session
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'vorstand@test.olzimmerberg.ch',
            ],
        ], $auth_request_repo->auth_requests);
        $this->assertSame([
            "INFO User login successful: vorstand@test.olzimmerberg.ch",
            "INFO   Auth: aktuell ftp",
            "INFO   Root: vorstand",
        ], $logger->handler->getPrettyRecords());
    }

    public function testAuthenticateWithWrongUsername(): void {
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
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
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
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
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $auth_request_repo->can_authenticate = false;
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
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
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $auth_utils->setSession($session);

        $result = $auth_utils->validateAccessToken('valid-token-1');

        $this->assertSame(FakeUsers::adminUser(), $result);
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
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
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
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
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
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $auth_request_repo->can_validate_access_token = false;
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setLogger($logger);
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

    public function testHasPermissionNoUser(): void {
        $entity_manager = new FakeEntityManager();
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
        $entity_manager = new FakeEntityManager();
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
        $entity_manager = new FakeEntityManager();
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
        $entity_manager = new FakeEntityManager();
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

    public function testGetAuthenticatedUserFromToken(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams(['access_token' => 'valid-token-1']);
        $auth_utils->setLogger($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(FakeUsers::adminUser(), $auth_utils->getAuthenticatedUser());
    }

    public function testGetAuthenticatedUserFromSession(): void {
        $entity_manager = new FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams([]);
        $auth_utils->setSession($session);
        $this->assertSame(FakeUsers::adminUser(), $auth_utils->getSessionUser());
    }

    public function testGetTokenUser(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $auth_utils = new AuthUtils();
        $auth_utils->setDateUtils($date_utils);
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams(['access_token' => 'valid-token-1']);
        $auth_utils->setLogger($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(FakeUsers::adminUser(), $auth_utils->getTokenUser());
    }

    public function testGetTokenUserForInvalidToken(): void {
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeAuthUtilsAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_request_repo = new FakeAuthUtilsAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = FakeLogger::create();
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setGetParams(['access_token' => 'invalid-token']);
        $auth_utils->setLogger($logger);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(null, $auth_utils->getTokenUser());
    }

    public function testGetSessionUser(): void {
        $entity_manager = new FakeEntityManager();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setEntityManager($entity_manager);
        $auth_utils->setSession($session);
        $this->assertSame(FakeUsers::adminUser(), $auth_utils->getSessionUser());
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
}
