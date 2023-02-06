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

    protected function getLogFileForDateTime(\DateTime $datetime): PlainLogFile {
        $data_path = $this->envUtils()->getDataPath();
        $logs_path = "{$data_path}logs/";
        $formatted = $datetime->format('Y-m-d');
        $file_path = "{$logs_path}merged-{$formatted}.log";
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return new PlainLogFile($file_path);
    }

    protected function getDateTimeForFilePath(string $file_path): \DateTime {
        $data_path = $this->envUtils()->getDataPath();
        $logs_path = "{$data_path}logs/";
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
