<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Roles\Role;
use Olz\Repository\Roles\PredefinedRole;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

#[AsCommand(name: 'olz:db-backup')]
class DbBackupCommand extends OlzCommand {
    /** Number of days for which daily backups are kept. */
    public const RETENTION_DAYS = 31;

    public const BACKUP_FILENAME_REGEX = '/^backup-([0-9]{4}-[0-9]{2}-[0-9]{2})\.crypt\.json$/';

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $key = $this->envUtils()->getDatabaseBackupKey();
        $backups_path = "{$this->envUtils()->getPrivatePath()}backups/";
        if (!is_dir($backups_path)) {
            mkdir($backups_path, 0o777, true);
        }

        $today = $this->dateUtils()->getIsoToday();
        $file_path = "{$backups_path}backup-{$today}.crypt.json";
        $this->devDataUtils()->writeDbBackup($key, $file_path);
        $this->logAndOutput("Database backup written to {$file_path}.");

        $num_pruned = $this->pruneOldBackups($backups_path);
        $this->logAndOutput("Pruned {$num_pruned} old database backup(s).");

        $day_of_month = intval(substr($today, 8, 2));
        if ($day_of_month !== 1) {
            $this->logAndOutput("Not the first of the month ({$today}). Not sending monthly backup email.", level: 'debug');
            return Command::SUCCESS;
        }

        $role_repo = $this->entityManager()->getRepository(Role::class);
        $role = $role_repo->getPredefinedRole(PredefinedRole::Sysadmin);
        $role_name = $role?->getUsername();
        if (!$role_name) {
            $this->logAndOutput("No sysadmin role email found!", level: 'error');
            return Command::SUCCESS;
        }
        $host = $this->envUtils()->getEmailForwardingHost();
        $recipient_email = "{$role_name}@{$host}";

        $text = <<<ZZZZZZZZZZ
            Hallo,

            Im Anhang findet ihr das monatliche Datenbank-Backup der OLZ-Webseite ({$today}).

            Die Datei ist mit dem `database_backup_key` verschlüsselt und kann bei Bedarf
            mit `tools/decrypt_backup/decrypt_backup.sh` wieder entschlüsselt werden.

            Liebe Grüsse
            OLZ-Website
            ZZZZZZZZZZ;

        $email = (new Email())
            ->to($recipient_email)
            ->subject("[OLZ] Monatliches Datenbank-Backup")
            ->text($text)
        ;
        $email->addPart(new DataPart(new File($file_path), "olz-website-{$today}.crypt.bak"));
        $this->emailUtils()->send($email);
        $this->logAndOutput("Sent monthly database backup email to {$recipient_email}.");

        return Command::SUCCESS;
    }

    protected function pruneOldBackups(string $backups_path): int {
        $retention_seconds = 86400 * self::RETENTION_DAYS;
        $cutoff = (strtotime("{$this->dateUtils()->getIsoToday()} 00:00:00") ?: 0) - $retention_seconds;
        $num_pruned = 0;
        $filenames = scandir($backups_path) ?: [];
        foreach ($filenames as $filename) {
            if (!preg_match(self::BACKUP_FILENAME_REGEX, $filename, $matches)) {
                continue;
            }
            $backup_timestamp = strtotime($matches[1]);
            if ($backup_timestamp === false || $backup_timestamp >= $cutoff) {
                continue;
            }
            unlink("{$backups_path}{$filename}");
            $num_pruned++;
        }
        return $num_pruned;
    }
}
