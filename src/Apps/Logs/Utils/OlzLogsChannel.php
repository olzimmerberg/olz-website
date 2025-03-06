<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\LogsUtils;
use Olz\Utils\WithUtilsTrait;

class OlzLogsChannel extends DailyFileLogsChannel {
    use WithUtilsTrait;

    public static function getId(): string {
        return 'olz-logs';
    }

    public static function getName(): string {
        return "OLZ Logs";
    }

    protected function getRetentionDays(): ?int {
        return LogsUtils::RETENTION_DAYS;
    }

    protected function getLogFileForDateTime(\DateTime $datetime): LogFileInterface {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $iso_date = $datetime->format('Y-m-d');
        $file_path = "{$logs_path}merged-{$iso_date}.log";
        $iso_today = $this->dateUtils()->getIsoToday();
        $hybrid_state = $iso_date < $iso_today ? HybridState::PREFER_GZ : HybridState::PREFER_PLAIN;
        return new HybridLogFile($file_path, "{$file_path}.gz", $file_path, $hybrid_state);
    }

    protected function getDateTimeForFilePath(string $file_path): \DateTime {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $esc_logs_path = preg_quote($logs_path, '/');
        $pattern = "/^{$esc_logs_path}merged\\-(\\d{4}\\-\\d{2}\\-\\d{2})\\.log$/";
        $res = preg_match($pattern, $file_path, $matches);
        if (!$res) {
            throw new \Exception("Not an OLZ Log file path: {$file_path}");
        }
        $iso_date = $matches[1];
        return new \DateTime($iso_date);
    }
}
