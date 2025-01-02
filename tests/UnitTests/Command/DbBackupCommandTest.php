<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\DbBackupCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\DbBackupCommand
 */
final class DbBackupCommandTest extends UnitTestCase {
    public function testDbBackupCommandSuccess(): void {
        $command = new DbBackupCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbBackupCommand...",
            "INFO Successfully ran command Olz\\Command\\DbBackupCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Command\DbBackupCommand...
            INFO: Successfully ran command Olz\Command\DbBackupCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['getDbBackup', 'some-secret-key'],
        ], WithUtilsCache::get('devDataUtils')->commands_called);
    }
}
