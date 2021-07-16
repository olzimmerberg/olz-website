<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/auth/AuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeAuthUtilsUserRepository {
    public function findOneBy($where) {
        if ($where === ['username' => 'admin']) {
            $admin_user = get_fake_user();
            $admin_user->setZugriff('all');
            return $admin_user;
        }
        if ($where === ['username' => 'specific']) {
            $specific_user = get_fake_user();
            $specific_user->setZugriff('test');
            return $specific_user;
        }
        if ($where === ['username' => 'no']) {
            $specific_user = get_fake_user();
            $specific_user->setZugriff('');
            return $specific_user;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \AuthUtils
 */
final class AuthUtilsTest extends UnitTestCase {
    public function testHasPermissionNoUser(): void {
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeAuthUtilsUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
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
        $user_repo = new FakeAuthUtilsUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
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
        $user_repo = new FakeAuthUtilsUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
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
        $user_repo = new FakeAuthUtilsUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
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
