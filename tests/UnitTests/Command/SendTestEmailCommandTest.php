<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SendTestEmailCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\SendTestEmailCommand
 */
final class SendTestEmailCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $command = new SendTestEmailCommand();
        $this->assertSame(SendTestEmailCommand::class, get_class($command));
    }
}
