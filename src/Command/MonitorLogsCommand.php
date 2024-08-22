<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:monitor-logs')]
class MonitorLogsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        if (!is_dir($logs_path)) {
            throw new \Exception("Expected {$logs_path} to be a directory");
        }

        $last_two_merged_log_file_contents = "";
        $merged_log_index = 0;
        foreach (scandir($logs_path, SCANDIR_SORT_DESCENDING) as $filename) {
            if ($merged_log_index > 1) {
                break;
            }
            if (preg_match('/^merged-.*\.log$/', $filename)) {
                $last_two_merged_log_file_contents = file_get_contents("{$logs_path}{$filename}").$last_two_merged_log_file_contents;
                $merged_log_index++;
            }
        }

        $now = new \DateTime();
        $minus_one_hour = \DateInterval::createFromDateString("-1 hours");
        $one_hour_ago = $now->add($minus_one_hour);

        $logs_in_last_hour = [];

        foreach (explode("\n", $last_two_merged_log_file_contents) as $line) {
            $res = preg_match('/^\[([0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})/', $line, $matches);
            if ($res) {
                $line_timestamp = strtotime($matches[1]);
                if ($line_timestamp > $one_hour_ago->getTimestamp()) {
                    $logs_in_last_hour[] = $line;
                }
            }
        }

        $output->writeln("Last hour: ".count($logs_in_last_hour)." logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isEmergencyLine($line); }))." emergency logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isAlertLine($line); }))." alert logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isCriticalLine($line); }))." critical logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isErrorLine($line); }))." error logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isWarningLine($line); }))." warning logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isNoticeLine($line); }))." notice logs");

        $this->checkEmergencies($logs_in_last_hour);
        $this->checkAlerts($logs_in_last_hour);
        $this->checkCritical($logs_in_last_hour);
        $this->checkManyErrors($logs_in_last_hour);
        $this->checkManyWarnings($logs_in_last_hour);
        $this->checkManyNotices($logs_in_last_hour);

        $output->writeln("OK:");
        return Command::SUCCESS;
    }

    /**
     * @param array<string> $logs_in_last_hour
     */
    protected function checkEmergencies(array $logs_in_last_hour): void {
        if (count(array_filter($logs_in_last_hour, function ($line) { return $this->isEmergencyLine($line); })) > 0) {
            throw new \Exception("Expected no emergencies");
        }
    }

    /**
     * @param array<string> $logs_in_last_hour
     */
    protected function checkAlerts(array $logs_in_last_hour): void {
        if (count(array_filter($logs_in_last_hour, function ($line) { return $this->isAlertLine($line); })) > 0) {
            throw new \Exception("Expected no alerts");
        }
    }

    /**
     * @param array<string> $logs_in_last_hour
     */
    protected function checkCritical(array $logs_in_last_hour): void {
        if (count(array_filter($logs_in_last_hour, function ($line) { return $this->isCriticalLine($line); })) > 0) {
            throw new \Exception("Expected no critical log entries");
        }
    }

    /**
     * @param array<string> $logs_in_last_hour
     */
    protected function checkManyErrors(array $logs_in_last_hour): void {
        $limit_per_hour = 1;

        $errors_per_hour = count(array_filter($logs_in_last_hour, function ($line) { return $this->isErrorLine($line); }));
        if ($errors_per_hour > $limit_per_hour) {
            throw new \Exception("Expected fewer error log entries per hour ({$errors_per_hour} > {$limit_per_hour})");
        }
    }

    /**
     * @param array<string> $logs_in_last_hour
     */
    protected function checkManyWarnings(array $logs_in_last_hour): void {
        $limit_per_hour = 10;

        $warnings_per_hour = count(array_filter($logs_in_last_hour, function ($line) { return $this->isWarningLine($line); }));
        if ($warnings_per_hour > $limit_per_hour) {
            throw new \Exception("Expected fewer warning log entries per hour ({$warnings_per_hour} > {$limit_per_hour})");
        }
    }

    /**
     * @param array<string> $logs_in_last_hour
     */
    protected function checkManyNotices(array $logs_in_last_hour): void {
        $limit_per_hour = 100;

        $notices_per_hour = count(array_filter($logs_in_last_hour, function ($line) { return $this->isNoticeLine($line); }));
        if ($notices_per_hour > $limit_per_hour) {
            throw new \Exception("Expected fewer notice log entries per hour ({$notices_per_hour} > {$limit_per_hour})");
        }
    }

    protected function isEmergencyLine(string $line): bool {
        return preg_match('/\.EMERGENCY\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.EMERGENCY\:/', $line) && !preg_match('/Olz\\\Command\\\Monitor/', $line);
    }

    protected function isAlertLine(string $line): bool {
        return preg_match('/\.ALERT\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.ALERT\:/', $line) && !preg_match('/Olz\\\Command\\\Monitor/', $line);
    }

    protected function isCriticalLine(string $line): bool {
        return preg_match('/\.CRITICAL\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.CRITICAL\:/', $line) && !preg_match('/Olz\\\Command\\\Monitor/', $line);
    }

    protected function isErrorLine(string $line): bool {
        return preg_match('/\.ERROR\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.ERROR\:/', $line) && !preg_match('/Olz\\\Command\\\Monitor/', $line);
    }

    protected function isWarningLine(string $line): bool {
        return preg_match('/\.WARNING\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.WARNING\:/', $line) && !preg_match('/Olz\\\Command\\\Monitor/', $line);
    }

    protected function isNoticeLine(string $line): bool {
        return preg_match('/\.NOTICE\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.NOTICE\:/', $line) && !preg_match('/Olz\\\Command\\\Monitor/', $line);
    }
}
