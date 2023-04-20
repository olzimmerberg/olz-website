<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\MonitorBackupCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\MonitorBackupCommand
 */
final class MonitorBackupCommandTest extends UnitTestCase {
    public function testMonitorBackupCommandSuccess(): void {
        $command = new MonitorBackupCommand();
        $this->assertSame(true, (bool) $command);
    }
}
