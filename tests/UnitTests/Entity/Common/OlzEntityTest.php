<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Entity\Common;

use Olz\Entity\Common\OlzEntity;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Entity\Common\OlzEntity
 */
final class OlzEntityTest extends UnitTestCase {
    public function testOlzEntityExists(): void {
        $user = new OlzEntity();
        $user->setDeletedAt(null);
        $user->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
        $this->assertTrue($user->exists());
        $user->setCreatedAt(new \DateTime('2020-03-13 19:30:01'));
        $this->assertFalse($user->exists());
    }

    public function testOlzEntityExistsAt(): void {
        $at = new \DateTime('2020-01-01 00:00:00');
        $user = new OlzEntity();
        // Creating in a second
        $user->setCreatedAt(new \DateTime('2020-01-01 00:00:01'));
        $this->assertFalse($user->exists($at));
        // Just created
        $user->setCreatedAt($at);
        $this->assertTrue($user->exists($at));
        // Deleting just now
        $user->setDeletedAt($at);
        $this->assertTrue($user->exists($at));
        // Just deleted
        $user->setDeletedAt(new \DateTime('2019-12-31 23:59:59'));
        $this->assertFalse($user->exists($at));
    }
}
