<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SendTestEmailCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 *
 * @covers \Olz\Command\SendTestEmailCommand
 */
final class SendTestEmailCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $message_bus = $this->createMock(MessageBusInterface::class);
        $command = new SendTestEmailCommand($message_bus);
        $this->assertSame(SendTestEmailCommand::class, get_class($command));
    }
}
