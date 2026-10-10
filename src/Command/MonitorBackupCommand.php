<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:monitor-backup')]
class MonitorBackupCommand extends OlzCommand {
    public const MAX_AGE_SECONDS = 86400 * 2;

    public const BACKUP_FILENAME_REGEX = '/^backup-([0-9]{4}-[0-9]{2}-[0-9]{2})\.crypt\.json$/';

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $backups_path = "{$this->envUtils()->getPrivatePath()}backups/";
        if (!is_dir($backups_path)) {
            throw new \Exception("Expected backups directory at {$backups_path}");
        }
        $latest_timestamp = $this->getLatestBackupTimestamp($backups_path);
        if ($latest_timestamp === null) {
            throw new \Exception("No database backup found in {$backups_path}");
        }
        $now_timestamp = strtotime($this->dateUtils()->getIsoNow()) ?: 0;
        $age_seconds = $now_timestamp - $latest_timestamp;
        if ($age_seconds > self::MAX_AGE_SECONDS) {
            $latest = date('Y-m-d', $latest_timestamp);
            throw new \Exception("Latest database backup ({$latest}) is older than 2 days");
        }
        $this->logAndOutput("OK:");
        return Command::SUCCESS;
    }

    protected function getLatestBackupTimestamp(string $backups_path): ?int {
        $latest_timestamp = null;
        $filenames = scandir($backups_path) ?: [];
        foreach ($filenames as $filename) {
            if (!preg_match(self::BACKUP_FILENAME_REGEX, $filename, $matches)) {
                continue;
            }
            $timestamp = strtotime($matches[1]);
            if ($timestamp === false) {
                continue;
            }
            if ($latest_timestamp === null || $timestamp > $latest_timestamp) {
                $latest_timestamp = $timestamp;
            }
        }
        return $latest_timestamp;
    }
}
