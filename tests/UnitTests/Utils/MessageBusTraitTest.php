<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MessageBusTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusTraitConcreteUtils {
    use MessageBusTrait;

    public function testOnlyMessageBus(): MessageBusInterface {
        return $this->messageBus();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\MessageBusTrait
 */
final class MessageBusTraitTest extends UnitTestCase {
    public function testSetGetMessageBus(): void {
        $utils = new MessageBusTraitConcreteUtils();
        $fake = $this->createMock(MessageBusInterface::class);
        $utils->setMessageBus($fake);
        $this->assertSame($fake, $utils->testOnlyMessageBus());
    }
}
