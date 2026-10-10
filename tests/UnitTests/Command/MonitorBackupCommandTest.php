<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\MonitorBackupCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\MonitorBackupCommand
 */
final class MonitorBackupCommandTest extends UnitTestCase {
    public function testMonitorBackupCommandSuccess(): void {
        $backups_path = $this->envUtils()->getPrivatePath().'backups/';
        mkdir($backups_path, 0o777, true);
        file_put_contents("{$backups_path}backup-2020-03-12.crypt.json", '{}');
        file_put_contents("{$backups_path}backup-2020-03-13.crypt.json", '{}');

        $command = new MonitorBackupCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\MonitorBackupCommand...",
            'INFO OK:',
            "INFO Successfully ran command Olz\\Command\\MonitorBackupCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
    }

    public function testMonitorBackupCommandNoDirectory(): void {
        $command = new MonitorBackupCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertStringContainsString('Expected backups directory at', $this->getLogs()[1] ?? '');
    }

    public function testMonitorBackupCommandNoBackup(): void {
        mkdir($this->envUtils()->getPrivatePath().'backups/', 0o777, true);
        $command = new MonitorBackupCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertStringContainsString('No database backup found in', $this->getLogs()[1] ?? '');
    }

    public function testMonitorBackupCommandTooOld(): void {
        $backups_path = $this->envUtils()->getPrivatePath().'backups/';
        mkdir($backups_path, 0o777, true);
        file_put_contents("{$backups_path}backup-2020-03-01.crypt.json", '{}');
        $command = new MonitorBackupCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertStringContainsString('is older than 2 days', $this->getLogs()[1] ?? '');
    }
}
