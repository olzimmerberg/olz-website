<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\MessageBusTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;
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
 * @covers \Olz\Common\Utils\MessageBusTrait
 */
final class MessageBusTraitTest extends UnitTestCase {
    public function testSetGetMessageBus(): void {
        $utils = new MessageBusTraitConcreteUtils();
        $fake = $this->createMock(MessageBusInterface::class);
        $utils->setMessageBus($fake);
        $this->assertSame($fake, $utils->testOnlyMessageBus());
    }
}
