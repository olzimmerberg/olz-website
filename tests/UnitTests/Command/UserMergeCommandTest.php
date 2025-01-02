<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\UserMergeCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\UserMergeCommand
 */
final class UserMergeCommandTest extends UnitTestCase {
    public function testUserMergeCommand(): void {
        $command = new UserMergeCommand();
        $this->assertTrue((bool) $command);
    }
}
