<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\DbBackupCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @internal
 *
 * @covers \Olz\Command\DbBackupCommand
 */
final class DbBackupCommandTest extends UnitTestCase {
    public function testDbBackupCommandSuccess(): void {
        $backups_path = $this->envUtils()->getPrivatePath().'backups/';
        mkdir($backups_path, 0o777, true);
        file_put_contents("{$backups_path}backup-2020-03-10.crypt.json", '{}');
        file_put_contents("{$backups_path}backup-2020-01-01.crypt.json", '{}');
        file_put_contents("{$backups_path}other-file.txt", 'other');

        $command = new DbBackupCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $expected_file_path = "{$backups_path}backup-2020-03-13.crypt.json";
        // Logs are sanitized by FakeLogHandler (see tests/Fake/FakeLogHandler.php).
        $expected_log_path = 'private-path/backups/backup-2020-03-13.crypt.json';
        $this->assertSame([
            "INFO Running command Olz\\Command\\DbBackupCommand...",
            "INFO Database backup written to {$expected_log_path}.",
            'INFO Pruned 1 old database backup(s).',
            'DEBUG Not the first of the month (2020-03-13). Not sending monthly backup email.',
            "INFO Successfully ran command Olz\\Command\\DbBackupCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<ZZZZZZZZZZ
            Running command Olz\\Command\\DbBackupCommand...
            Database backup written to {$expected_file_path}.
            Pruned 1 old database backup(s).
            Not the first of the month (2020-03-13). Not sending monthly backup email.
            Successfully ran command Olz\\Command\\DbBackupCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['writeDbBackup', 'some-secret-key', $expected_file_path],
        ], WithUtilsCache::get('devDataUtils')->commands_called);
        $this->assertFileDoesNotExist("{$backups_path}backup-2020-01-01.crypt.json");
        $this->assertFileExists("{$backups_path}backup-2020-03-10.crypt.json");
        $this->assertFileExists("{$backups_path}other-file.txt");
    }

    public function testDbBackupCommandSendsEmailFirstOfMonth(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('dateUtils')->testOnlySetDate('2020-03-01 19:30:00');
        WithUtilsCache::get('emailUtils')->setMailer($mailer);

        $backups_path = $this->envUtils()->getPrivatePath().'backups/';
        mkdir($backups_path, 0o777, true);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $emails = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails[] = $email;
                return true;
            }),
            null,
        );

        $command = new DbBackupCommand();
        $return_code = $command->run($input, $output);

        $expected_file_path = "{$backups_path}backup-2020-03-01.crypt.json";
        // Logs are sanitized by FakeLogHandler (see tests/Fake/FakeLogHandler.php).
        $expected_log_path = 'private-path/backups/backup-2020-03-01.crypt.json';
        $this->assertSame([
            "INFO Running command Olz\\Command\\DbBackupCommand...",
            "INFO Database backup written to {$expected_log_path}.",
            'INFO Pruned 0 old database backup(s).',
            'DEBUG Sending email to website.fake@staging.olzimmerberg.ch ()',
            'INFO Sent monthly database backup email to website.fake@staging.olzimmerberg.ch.',
            "INFO Successfully ran command Olz\\Command\\DbBackupCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<ZZZZZZZZZZ
            Running command Olz\\Command\\DbBackupCommand...
            Database backup written to {$expected_file_path}.
            Pruned 0 old database backup(s).
            Sent monthly database backup email to website.fake@staging.olzimmerberg.ch.
            Successfully ran command Olz\\Command\\DbBackupCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['writeDbBackup', 'some-secret-key', $expected_file_path],
        ], WithUtilsCache::get('devDataUtils')->commands_called);

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: website.fake@staging.olzimmerberg.ch
                Cc: 
                Bcc: 
                Subject: [OLZ] Monatliches Datenbank-Backup

                Hallo,

                Im Anhang findet ihr das monatliche Datenbank-Backup der OLZ-Webseite (2020-03-01).
                
                Die Datei ist mit dem `database_backup_key` verschlüsselt und kann bei Bedarf
                mit `tools/decrypt_backup/decrypt_backup.sh` wieder entschlüsselt werden.
                
                Liebe Grüsse
                OLZ-Website
                
                (no html body)
                
                olz-website-2020-03-01.crypt.bak
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $emails));
    }
}
