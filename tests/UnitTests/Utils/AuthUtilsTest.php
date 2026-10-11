<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\WithUtilsCache;

class TestOnlyAuthUtils extends AuthUtils {
    public function hashPassword(string $password): string {
        return md5($password); // just for test
    }

    public function verifyPassword(string $password, string $hash): bool {
        return md5($password) === $hash; // just for test
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthUtils
 */
final class AuthUtilsTest extends UnitTestCase {
    public function testAuthenticateWithCorrectCredentials(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new TestOnlyAuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        // Also test that it's resolving admin-old to admin
        $result = $auth_utils->authenticate('admin-old', 'adm1n');

        $this->assertSame(FakeUser::adminUser(), $result);
        $this->assertSame([
            'user' => 'inexistent', // for now, we don't modify the session
        ], WithUtilsCache::get('session')->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED',
                'timestamp' => null,
                'username' => 'admin-old',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "INFO User login successful: admin-old",
            "INFO   Auth: all verified_email",
            "INFO   Root: karten",
        ], $this->getLogs());
    }

    public function testAuthenticateWithWrongUsername(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

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
        ], WithUtilsCache::get('session')->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_CREDENTIALS',
                'timestamp' => null,
                'username' => 'wrooong',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "NOTICE Login attempt with invalid credentials from IP: 1.2.3.4 (user: wrooong).",
        ], $this->getLogs());
    }

    public function testAuthenticateWithWrongPassword(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

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
        ], WithUtilsCache::get('session')->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_CREDENTIALS',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "NOTICE Login attempt with invalid credentials from IP: 1.2.3.4 (user: admin).",
        ], $this->getLogs());
    }

    public function testAuthenticateCanNotAuthenticate(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[AuthRequest::class]->num_remaining_attempts = 0;
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

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
        ], WithUtilsCache::get('session')->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'BLOCKED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "NOTICE Login attempt from blocked IP: 1.2.3.4 (user: admin).",
        ], $this->getLogs());
    }

    public function testValidateValidAccessToken(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $auth_utils->validateAccessToken('valid-token');

        $this->assertSame(FakeUser::adminUser(), $result);
        $this->assertSame([
            'user' => 'inexistent', // for now, we don't modify the session
        ], WithUtilsCache::get('session')->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'TOKEN_VALIDATED',
                'timestamp' => null,
                'username' => 'admin',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "INFO Token validation successful: 2",
        ], $this->getLogs());
    }

    public function testValidateInvalidAccessToken(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

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
        ], WithUtilsCache::get('session')->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'INVALID_TOKEN',
                'timestamp' => null,
                'username' => '',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "NOTICE Invalid access token validation from IP: 1.2.3.4.",
        ], $this->getLogs());
    }

    public function testValidateExpiredAccessToken(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $auth_utils->validateAccessToken('expired-token');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Expired access token validation from IP: 1.2.3.4.',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], WithUtilsCache::get('session')->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'EXPIRED_TOKEN',
                'timestamp' => null,
                'username' => '',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "NOTICE Expired access token validation from IP: 1.2.3.4.",
        ], $this->getLogs());
    }

    public function testValidateAccessTokenCanNotValidate(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[AuthRequest::class]->can_validate_access_token = false;
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];

        $auth_utils = new AuthUtils();
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $auth_utils->validateAccessToken('valid-token');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Access token validation from blocked IP: 1.2.3.4.',
                $exc->getMessage()
            );
        }

        $this->assertSame([
            'user' => 'inexistent',
        ], WithUtilsCache::get('session')->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'TOKEN_BLOCKED',
                'timestamp' => null,
                'username' => '',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame([
            "NOTICE Access token validation from blocked IP: 1.2.3.4.",
        ], $this->getLogs());
    }

    public function testResolveUsername(): void {
        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testResolveOldUsername(): void {
        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin-old');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testResolveEmail(): void {
        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('vorstand@olzimmerberg.ch');
        $this->assertSame(FakeUser::vorstandUser(), $result);
    }

    public function testResolveUsernameEmail(): void {
        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin@olzimmerberg.ch');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testResolveOldUsernameEmail(): void {
        $auth_utils = new AuthUtils();

        $result = $auth_utils->resolveUsernameOrEmail('admin-old@olzimmerberg.ch');
        $this->assertSame(FakeUser::adminUser(), $result);
    }

    public function testHasPermissionNoUser(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'inexistent',
        ];
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasPermission('test'));
        $this->assertFalse($auth_utils->hasPermission('other'));
        $this->assertFalse($auth_utils->hasPermission('all'));
        $this->assertFalse($auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithNoPermission(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'no',
        ];
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasPermission('test'));
        $this->assertFalse($auth_utils->hasPermission('other'));
        $this->assertFalse($auth_utils->hasPermission('all'));
        $this->assertTrue($auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithSpecificPermission(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'specific',
        ];
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasPermission('test'));
        $this->assertFalse($auth_utils->hasPermission('other'));
        $this->assertFalse($auth_utils->hasPermission('all'));
        $this->assertTrue($auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithAllPermissions(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasPermission('test'));
        $this->assertTrue($auth_utils->hasPermission('other'));
        $this->assertTrue($auth_utils->hasPermission('all'));
        $this->assertTrue($auth_utils->hasPermission('any'));
    }

    public function testHasPermissionWithRolePermissions(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasPermission('vorstand_user'));
        $this->assertTrue($auth_utils->hasPermission('vorstand_role'));
        $this->assertFalse($auth_utils->hasPermission('all'));
        $this->assertTrue($auth_utils->hasPermission('any'));
    }

    public function testHasUserPermissionNoUser(): void {
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasUserPermission('test', null));
        $this->assertFalse($auth_utils->hasUserPermission('other', null));
        $this->assertFalse($auth_utils->hasUserPermission('all', null));
        $this->assertFalse($auth_utils->hasUserPermission('any', null));
    }

    public function testHasUserPermissionWithNoPermission(): void {
        $user = new User();
        $user->setPermissions('');
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasUserPermission('test', $user));
        $this->assertFalse($auth_utils->hasUserPermission('other', $user));
        $this->assertFalse($auth_utils->hasUserPermission('all', $user));
        $this->assertTrue($auth_utils->hasUserPermission('any', $user));
    }

    public function testHasUserPermissionWithSpecificPermission(): void {
        $user = new User();
        $user->setPermissions(' test ');
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasUserPermission('test', $user));
        $this->assertFalse($auth_utils->hasUserPermission('other', $user));
        $this->assertFalse($auth_utils->hasUserPermission('all', $user));
        $this->assertTrue($auth_utils->hasUserPermission('any', $user));
    }

    public function testHasUserPermissionWithAllPermissions(): void {
        $user = new User();
        $user->setPermissions(' all ');
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasUserPermission('test', $user));
        $this->assertTrue($auth_utils->hasUserPermission('other', $user));
        $this->assertTrue($auth_utils->hasUserPermission('all', $user));
        $this->assertTrue($auth_utils->hasUserPermission('any', $user));
    }

    public function testHasUserPermissionWithRolePermissions(): void {
        $user = new User();
        $user->setPermissions('aktuell ftp vorstand_user');
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasUserPermission('vorstand_user', $user));
        $this->assertFalse($auth_utils->hasUserPermission('vorstand_role', $user));
        $this->assertFalse($auth_utils->hasUserPermission('all', $user));
        $this->assertTrue($auth_utils->hasUserPermission('any', $user));
    }

    public function testHasRolePermissionNoRole(): void {
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasRolePermission('test', null));
        $this->assertFalse($auth_utils->hasRolePermission('other', null));
        $this->assertFalse($auth_utils->hasRolePermission('all', null));
        $this->assertFalse($auth_utils->hasRolePermission('any', null));
    }

    public function testHasRolePermissionWithNoPermission(): void {
        $role = new Role();
        $role->setOnOff(1);
        $role->setPermissions('');
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasRolePermission('test', $role));
        $this->assertFalse($auth_utils->hasRolePermission('other', $role));
        $this->assertFalse($auth_utils->hasRolePermission('all', $role));
        $this->assertTrue($auth_utils->hasRolePermission('any', $role));
    }

    public function testHasRolePermissionWithSpecificPermission(): void {
        $role = new Role();
        $role->setOnOff(1);
        $role->setPermissions('test');
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasRolePermission('test', $role));
        $this->assertFalse($auth_utils->hasRolePermission('other', $role));
        $this->assertFalse($auth_utils->hasRolePermission('all', $role));
        $this->assertTrue($auth_utils->hasRolePermission('any', $role));
    }

    public function testHasRolePermissionWithAllPermissions(): void {
        $role = new Role();
        $role->setOnOff(1);
        $role->setPermissions('all');
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->hasRolePermission('test', $role));
        $this->assertTrue($auth_utils->hasRolePermission('other', $role));
        $this->assertTrue($auth_utils->hasRolePermission('all', $role));
        $this->assertTrue($auth_utils->hasRolePermission('any', $role));
    }

    public function testHasSoftDeletedRolePermissionWithNoPermission(): void {
        $role = new Role();
        $role->setOnOff(0);
        $role->setPermissions('');
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasRolePermission('test', $role));
        $this->assertFalse($auth_utils->hasRolePermission('other', $role));
        $this->assertFalse($auth_utils->hasRolePermission('all', $role));
        $this->assertFalse($auth_utils->hasRolePermission('any', $role));
    }

    public function testHasSoftDeletedRolePermissionWithSpecificPermission(): void {
        $role = new Role();
        $role->setOnOff(0);
        $role->setPermissions('test');
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasRolePermission('test', $role));
        $this->assertFalse($auth_utils->hasRolePermission('other', $role));
        $this->assertFalse($auth_utils->hasRolePermission('all', $role));
        $this->assertFalse($auth_utils->hasRolePermission('any', $role));
    }

    public function testHasSoftDeletedRolePermissionWithAllPermissions(): void {
        $role = new Role();
        $role->setOnOff(0);
        $role->setPermissions('all');
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->hasRolePermission('test', $role));
        $this->assertFalse($auth_utils->hasRolePermission('other', $role));
        $this->assertFalse($auth_utils->hasRolePermission('all', $role));
        $this->assertFalse($auth_utils->hasRolePermission('any', $role));
    }

    public function testGetCurrentUserFromToken(): void {
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams(['access_token' => 'valid-token']);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getCurrentUser());
    }

    public function testGetCurrentUserFromSession(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getCurrentUser());
    }

    public function testGetTokenUser(): void {
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams(['access_token' => 'valid-token']);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getTokenUser());
    }

    public function testGetTokenUserForInvalidToken(): void {
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams(['access_token' => 'invalid-token']);
        $auth_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertNull($auth_utils->getTokenUser());
    }

    public function testGetSessionUser(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $this->assertSame(FakeUser::vorstandUser(), $auth_utils->getSessionUser());
    }

    public function testGetCurrentAuthUserFromSession(): void {
        WithUtilsCache::get('session')->session_storage = [
            'auth_user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertSame(FakeUser::adminUser(), $auth_utils->getCurrentAuthUser());
    }

    public function testGetSessionAuthUser(): void {
        WithUtilsCache::get('session')->session_storage = [
            'auth_user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $this->assertSame(FakeUser::vorstandUser(), $auth_utils->getSessionAuthUser());
    }

    public function testGetAuthenticatedRoles(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertSame(['admin_role'], array_map(function ($role) {
            return $role->getUsername();
        }, $auth_utils->getAuthenticatedRoles() ?? []));
    }

    public function testGetAuthenticatedRolesUnauthenticated(): void {
        WithUtilsCache::get('session')->session_storage = [];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertNull($auth_utils->getAuthenticatedRoles());
    }

    public function testGetAuthenticatedRolesAdmin(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(['admin_role'], array_map(function ($role) {
            return $role->getUsername();
        }, $auth_utils->getAuthenticatedRoles(FakeUser::adminUser()) ?? []));
    }

    public function testGetAuthenticatedRolesVorstand(): void {
        $auth_utils = new AuthUtils();
        $this->assertSame(['vorstand-role'], array_map(function ($role) {
            return $role->getUsername();
        }, $auth_utils->getAuthenticatedRoles(FakeUser::vorstandUser()) ?? []));
    }

    public function testIsRoleIdAuthenticated(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertFalse($auth_utils->isRoleIdAuthenticated(1));
        $this->assertTrue($auth_utils->isRoleIdAuthenticated(2));
        $this->assertFalse($auth_utils->isRoleIdAuthenticated(3));
    }

    public function testIsRoleIdAuthenticatedUnauthenticated(): void {
        WithUtilsCache::get('session')->session_storage = [];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertFalse($auth_utils->isRoleIdAuthenticated(1));
        $this->assertFalse($auth_utils->isRoleIdAuthenticated(2));
        $this->assertFalse($auth_utils->isRoleIdAuthenticated(3));
    }

    public function testHasRoleEditPermissionAdmin(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'admin',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertTrue($auth_utils->hasRoleEditPermission(1));
        $this->assertTrue($auth_utils->hasRoleEditPermission(2));
        $this->assertTrue($auth_utils->hasRoleEditPermission(3));
    }

    public function testHasRoleEditPermissionVorstand(): void {
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'vorstand',
        ];
        $auth_utils = new AuthUtils();
        $auth_utils->setGetParams([]);
        $this->assertFalse($auth_utils->hasRoleEditPermission(1));
        $this->assertFalse($auth_utils->hasRoleEditPermission(2));
        $this->assertTrue($auth_utils->hasRoleEditPermission(3));
        $this->assertTrue($auth_utils->hasRoleEditPermission(33));
        $this->assertTrue($auth_utils->hasRoleEditPermission(333));
        $this->assertTrue($auth_utils->hasRoleEditPermission(3333));
        $this->assertTrue($auth_utils->hasRoleEditPermission(33333));
        $this->assertFalse($auth_utils->hasRoleEditPermission(333333));
    }

    public function testIsUsernameAllowed(): void {
        $auth_utils = new AuthUtils();
        $this->assertTrue($auth_utils->isUsernameAllowed('testTEST1234.-_'));
        $this->assertFalse($auth_utils->isUsernameAllowed('test@wtf'));
        $this->assertFalse($auth_utils->isUsernameAllowed('ötzi'));
        $this->assertFalse($auth_utils->isUsernameAllowed('\';DROP TABLE users;'));
    }

    public function testIsPasswordAllowed(): void {
        $auth_utils = new AuthUtils();
        $this->assertFalse($auth_utils->isPasswordAllowed('test'));
        $this->assertTrue($auth_utils->isPasswordAllowed('longpassword'));
        $this->assertFalse($auth_utils->isPasswordAllowed('1234567'));
        $this->assertTrue($auth_utils->isPasswordAllowed('12345678'));
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

        $this->assertSame(
            [
                '2x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$256.jpg',
                '1x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$128.jpg',
            ],
            $auth_utils->getUserAvatar(FakeUser::maximal())
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
        $user->setLastName('Äbersold');
        $this->assertSame(
            ['1x' => '/_/assets/user_initials_%C3%96%C3%84.svg'],
            $auth_utils->getUserAvatar($user)
        );
    }

    public function testHashPassword(): void {
        $auth_utils = new AuthUtils();
        $passowrd_hash = $auth_utils->hashPassword('strong');
        $this->assertTrue(password_verify('strong', $passowrd_hash));
        $this->assertFalse(password_verify('wrong', $passowrd_hash));
    }

    public function testVerifyPassword(): void {
        $auth_utils = new AuthUtils();
        $passowrd_hash = password_hash('strong', PASSWORD_DEFAULT);
        $this->assertTrue($auth_utils->verifyPassword('strong', $passowrd_hash));
        $this->assertFalse($auth_utils->verifyPassword('wrong', $passowrd_hash));
    }

    public function testIssueRefreshToken(): void {
        $auth_utils = new FakeAuthUtils();
        $entity_manager = WithUtilsCache::get('entityManager');

        $raw_token = $auth_utils->issueRefreshToken(FakeUser::adminUser());

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]{43}$/', $raw_token);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $access_token = $entity_manager->flushed_persisted[0];
        $this->assertInstanceOf(AccessToken::class, $access_token);
        $this->assertSame('refresh', $access_token->getPurpose());
        $this->assertSame(hash('sha256', $raw_token), $access_token->getToken());
        $this->assertNotSame($raw_token, $access_token->getToken());
        $this->assertSame(FakeUser::adminUser(), $access_token->getUser());
        $this->assertSame('2021-03-13 19:30:00', $access_token->getExpiresAt()?->format('Y-m-d H:i:s'));
    }

    public function testCreateRefreshTokenCookie(): void {
        $auth_utils = new FakeAuthUtils();

        $cookie = $auth_utils->createRefreshTokenCookie('raw-token');

        $this->assertSame('olz_refresh_token', $cookie->getName());
        $this->assertSame('raw-token', $cookie->getValue());
        $this->assertSame('/', $cookie->getPath());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertTrue($cookie->isSecure());
        $this->assertSame('lax', $cookie->getSameSite());
    }

    public function testCreateClearingRefreshTokenCookie(): void {
        $auth_utils = new FakeAuthUtils();

        $cookie = $auth_utils->createRefreshTokenCookie(null);

        $this->assertSame('', $cookie->getValue());
        $this->assertLessThan(time(), $cookie->getExpiresTime());
    }

    public function testRevokeAllRefreshTokens(): void {
        $auth_utils = new FakeAuthUtils();
        $user = FakeUser::adminUser();
        $token1 = $this->createRefreshToken('token-1', $user, '2021-01-01 00:00:00');
        $token2 = $this->createRefreshToken('token-2', $user, '2021-01-01 00:00:00');
        $this->mockRefreshTokens([$token1, $token2]);
        $entity_manager = WithUtilsCache::get('entityManager');

        $auth_utils->revokeAllRefreshTokens($user);

        $this->assertSame([$token1, $token2], $entity_manager->flushed_removed);
    }

    protected function createRefreshToken(
        string $raw_token,
        User $user,
        string $expires_at,
    ): AccessToken {
        $access_token = new AccessToken();
        $access_token->setUser($user);
        $access_token->setPurpose('refresh');
        $access_token->setToken(hash('sha256', $raw_token));
        $access_token->setCreatedAt(new \DateTime('2020-01-01 00:00:00'));
        $access_token->setExpiresAt(new \DateTime($expires_at));
        return $access_token;
    }

    /** @param array<AccessToken> $access_tokens */
    protected function mockRefreshTokens(array $access_tokens): void {
        $repo = WithUtilsCache::get('entityManager')->repositories[AccessToken::class];
        $repo->entitiesToBeFoundForQuery = function (array $criteria) use ($access_tokens): array {
            return array_values(array_filter($access_tokens, function (AccessToken $access_token) use ($criteria): bool {
                foreach ($criteria as $field => $value) {
                    if ($field === 'user') {
                        $filter_user_id = $value instanceof User ? $value->getId() : null;
                        if ($access_token->getUser()?->getId() !== $filter_user_id) {
                            return false;
                        }
                    } elseif ($access_token->testOnlyGetField($field) !== $value) {
                        return false;
                    }
                }
                return true;
            }));
        };
    }
}
