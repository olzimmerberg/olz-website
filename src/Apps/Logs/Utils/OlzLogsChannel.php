<?php

namespace Olz\Apps\Logs\Utils;

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
        return 366;
    }

    protected function getLogFileForDateTime(\DateTime $datetime): LogFileInterface {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $iso_date = $datetime->format('Y-m-d');
        $plain_path = "{$logs_path}merged-{$iso_date}.log";
        $gz_path = "{$plain_path}.gz";
        $index_path = "{$plain_path}.index.json.gz";
        $iso_today = $this->dateUtils()->getIsoToday();
        if ($iso_date < $iso_today) {
            return new HybridLogFile($gz_path, $plain_path, $index_path);
        }
        return new PlainLogFile($plain_path, $index_path);
    }

    protected function getDateTimeForFilePath(string $file_path): \DateTime {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $esc_logs_path = preg_quote($logs_path, '/');
        $pattern = "/^{$esc_logs_path}merged\\-(\\d{4}\\-\\d{2}\\-\\d{2})\\.log(\\.gz)?$/";
        $res = preg_match($pattern, $file_path, $matches);
        if (!$res) {
            throw new \Exception("Not an OLZ Log file path: {$file_path}");
        }
        $iso_date = $matches[1];
        return new \DateTime($iso_date);
    }
}
