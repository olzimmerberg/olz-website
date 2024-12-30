<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Entity\Users;

use Olz\Entity\Users\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Entity\Users\User
 */
final class UserTest extends UnitTestCase {
    public function testUserGetPermissionMap(): void {
        $user = new User();
        $user->setPermissions(' test run ');
        $this->assertSame(
            ['test' => true, 'run' => true],
            $user->getPermissionMap()
        );
    }

    public function testUserGetPermissionMapEmpty(): void {
        $user = new User();
        $user->setPermissions(' ');
        $this->assertSame([], $user->getPermissionMap());
        $user->setPermissions('');
        $this->assertSame([], $user->getPermissionMap());
    }

    public function testUserSetPermissionMapOverwrite(): void {
        $user = new User();
        $user->setPermissions(' test run ');
        $user->setPermissionMap(['new' => true, 'run' => true]);
        $this->assertSame(' new run ', $user->getPermissions());
    }

    public function testUserSetPermissionMap(): void {
        $user = new User();
        $user->setPermissionMap(['test' => true, 'not' => false]);
        $this->assertSame(' test ', $user->getPermissions());
    }

    public function testUserAddPermission(): void {
        $user = new User();
        $user->setPermissions(' test run ');
        $user->addPermission('new');
        $this->assertSame(' test run new ', $user->getPermissions());
    }

    public function testUserRemovePermission(): void {
        $user = new User();
        $user->setPermissions(' test run ');
        $user->removePermission('test');
        $this->assertSame(' run ', $user->getPermissions());
    }
}
