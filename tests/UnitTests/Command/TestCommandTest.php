<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\TestCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\TestCommand
 */
final class TestCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $command = new TestCommand();
        $this->assertSame(true, (bool) $command);
    }
}
