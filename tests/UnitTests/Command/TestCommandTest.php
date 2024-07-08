<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\TestCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 *
 * @covers \Olz\Command\TestCommand
 */
final class TestCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $message_bus = $this->createMock(MessageBusInterface::class);
        $command = new TestCommand($message_bus);
        $this->assertTrue((bool) $command);
    }
}
