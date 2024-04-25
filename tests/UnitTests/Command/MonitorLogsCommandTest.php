<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\MonitorLogsCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\MonitorLogsCommand
 */
final class MonitorLogsCommandTest extends UnitTestCase {
    public function testMonitorLogsCommandSuccess(): void {
        $command = new MonitorLogsCommand();
        $this->assertTrue((bool) $command);
    }
}
