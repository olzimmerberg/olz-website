<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\LogForAnHourCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\LogForAnHourCommand
 */
final class LogForAnHourCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $command = new LogForAnHourCommand();
        $this->assertTrue((bool) $command);
    }
}
