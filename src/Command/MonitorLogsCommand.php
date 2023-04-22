<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:monitor-logs')]
class MonitorLogsCommand extends OlzCommand {
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $data_path = $this->envUtils()->getDataPath();
        $logs_path = "{$data_path}logs/";
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

        $now = new \DateTime();
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $one_day_ago = $now->add($minus_one_day);

        $logs_in_last_hour = [];
        $logs_in_last_day = [];

        foreach (explode("\n", $last_two_merged_log_file_contents) as $line) {
            $res = preg_match('/^\[([0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})/', $line, $matches);
            if ($res) {
                $line_timestamp = strtotime($matches[1]);
                if ($line_timestamp > $one_hour_ago->getTimestamp()) {
                    $logs_in_last_hour[] = $line;
                }
                if ($line_timestamp > $one_day_ago->getTimestamp()) {
                    $logs_in_last_day[] = $line;
                }
            }
        }

        $output->writeln("Last hour: ".count($logs_in_last_hour)." logs");
        $output->writeln("Last day: ".count($logs_in_last_day)." logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isEmergencyLine($line); }))." emergency logs");
        $output->writeln("Last day: ".count(array_filter($logs_in_last_day, function ($line) { return $this->isEmergencyLine($line); }))." emergency logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isAlertLine($line); }))." alert logs");
        $output->writeln("Last day: ".count(array_filter($logs_in_last_day, function ($line) { return $this->isAlertLine($line); }))." alert logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isCriticalLine($line); }))." critical logs");
        $output->writeln("Last day: ".count(array_filter($logs_in_last_day, function ($line) { return $this->isCriticalLine($line); }))." critical logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isErrorLine($line); }))." error logs");
        $output->writeln("Last day: ".count(array_filter($logs_in_last_day, function ($line) { return $this->isErrorLine($line); }))." error logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isWarningLine($line); }))." warning logs");
        $output->writeln("Last day: ".count(array_filter($logs_in_last_day, function ($line) { return $this->isWarningLine($line); }))." warning logs");

        $output->writeln("Last hour: ".count(array_filter($logs_in_last_hour, function ($line) { return $this->isNoticeLine($line); }))." notice logs");
        $output->writeln("Last day: ".count(array_filter($logs_in_last_day, function ($line) { return $this->isNoticeLine($line); }))." notice logs");

        $this->checkEmergencies($logs_in_last_hour, $logs_in_last_day);
        $this->checkAlerts($logs_in_last_hour, $logs_in_last_day);
        $this->checkCritical($logs_in_last_hour, $logs_in_last_day);
        $this->checkManyErrors($logs_in_last_hour, $logs_in_last_day);
        $this->checkManyWarnings($logs_in_last_hour, $logs_in_last_day);
        $this->checkManyNotices($logs_in_last_hour, $logs_in_last_day);

        $output->writeln("OK:");
        return Command::SUCCESS;
    }

    protected function checkEmergencies($logs_in_last_hour, $logs_in_last_day) {
        if (count(array_filter($logs_in_last_hour, function ($line) { return $this->isEmergencyLine($line); })) > 0) {
            throw new \Exception("Expected no emergencies");
        }
    }

    protected function checkAlerts($logs_in_last_hour, $logs_in_last_day) {
        if (count(array_filter($logs_in_last_hour, function ($line) { return $this->isAlertLine($line); })) > 0) {
            throw new \Exception("Expected no alerts");
        }
    }

    protected function checkCritical($logs_in_last_hour, $logs_in_last_day) {
        if (count(array_filter($logs_in_last_hour, function ($line) { return $this->isCriticalLine($line); })) > 0) {
            throw new \Exception("Expected no critical log entries");
        }
    }

    protected function checkManyErrors($logs_in_last_hour, $logs_in_last_day) {
        $limit_per_hour = 1;
        $limit_per_day = 5;

        $errors_per_hour = count(array_filter($logs_in_last_hour, function ($line) { return $this->isErrorLine($line); }));
        if ($errors_per_hour > $limit_per_hour) {
            throw new \Exception("Expected fewer error log entries per hour ({$errors_per_hour} > {$limit_per_hour})");
        }

        $errors_per_day = count(array_filter($logs_in_last_day, function ($line) { return $this->isErrorLine($line); }));
        if ($errors_per_day > $limit_per_day) {
            throw new \Exception("Expected fewer error log entries per day ({$errors_per_day} > {$limit_per_day})");
        }
    }

    protected function checkManyWarnings($logs_in_last_hour, $logs_in_last_day) {
        $limit_per_hour = 10;
        $limit_per_day = 50;

        $warnings_per_hour = count(array_filter($logs_in_last_hour, function ($line) { return $this->isWarningLine($line); }));
        if ($warnings_per_hour > $limit_per_hour) {
            throw new \Exception("Expected fewer warning log entries per hour ({$warnings_per_hour} > {$limit_per_hour})");
        }

        $warnings_per_day = count(array_filter($logs_in_last_day, function ($line) { return $this->isWarningLine($line); }));
        if ($warnings_per_day > $limit_per_day) {
            throw new \Exception("Expected fewer warning log entries per day ({$warnings_per_day} > {$limit_per_day})");
        }
    }

    protected function checkManyNotices($logs_in_last_hour, $logs_in_last_day) {
        $limit_per_hour = 100;
        $limit_per_day = 5000;

        $notices_per_hour = count(array_filter($logs_in_last_hour, function ($line) { return $this->isNoticeLine($line); }));
        if ($notices_per_hour > $limit_per_hour) {
            throw new \Exception("Expected fewer notice log entries per hour ({$notices_per_hour} > {$limit_per_hour})");
        }

        $notices_per_day = count(array_filter($logs_in_last_day, function ($line) { return $this->isNoticeLine($line); }));
        if ($notices_per_day > $limit_per_day) {
            throw new \Exception("Expected fewer notice log entries per day ({$notices_per_day} > {$limit_per_day})");
        }
    }

    protected function isEmergencyLine($line) {
        return preg_match('/\.EMERGENCY\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.EMERGENCY\:/', $line) && !preg_match('/Olz\\\\Command\\\\Monitor/', $line);
    }

    protected function isAlertLine($line) {
        return preg_match('/\.ALERT\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.ALERT\:/', $line) && !preg_match('/Olz\\\\Command\\\\Monitor/', $line);
    }

    protected function isCriticalLine($line) {
        return preg_match('/\.CRITICAL\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.CRITICAL\:/', $line) && !preg_match('/Olz\\\\Command\\\\Monitor/', $line);
    }

    protected function isErrorLine($line) {
        return preg_match('/\.ERROR\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.ERROR\:/', $line) && !preg_match('/Olz\\\\Command\\\\Monitor/', $line);
    }

    protected function isWarningLine($line) {
        return preg_match('/\.WARNING\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.WARNING\:/', $line) && !preg_match('/Olz\\\\Command\\\\Monitor/', $line);
    }

    protected function isNoticeLine($line) {
        return preg_match('/\.NOTICE\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.NOTICE\:/', $line) && !preg_match('/Olz\\\\Command\\\\Monitor/', $line);
    }
}
