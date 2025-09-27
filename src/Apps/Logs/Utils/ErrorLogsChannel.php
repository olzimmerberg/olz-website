<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class ErrorLogsChannel extends LogrotateLogsChannel {
    use WithUtilsTrait;

    public static function getId(): string {
        return 'error-logs';
    }

    public static function getName(): string {
        return "Error Logs";
    }

    protected function getLogFileForIndex(int $index): LogFileInterface {
        $log_name = 'error_log';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $file_path = "{$syslog_path}{$log_name}.{$index}";
        if ($index === 0) {
            $file_path = "{$syslog_path}{$log_name}";
        }
        $index_path = "{$file_path}.index.json.gz";
        if (is_file($file_path)) {
            return new PlainLogFile($file_path, $index_path);
        }
        if (is_file("{$file_path}.gz")) {
            return new HybridLogFile("{$file_path}.gz", $file_path, $index_path);
        }
        throw new \Exception("No such file: {$file_path}");
    }

    protected function getIndexForFilePath(string $file_path): int {
        $log_name = 'error_log';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $esc_syslog_path = preg_quote($syslog_path, '/');
        $pattern = "/^{$esc_syslog_path}{$log_name}($|\\.(\\d+)$)/";
        $res = preg_match($pattern, $file_path, $matches);
        if (!$res) {
            throw new \Exception("Not an OLZ Log file path: {$file_path}");
        }
        if ($matches[1] === '') {
            return 0;
        }
        return intval($matches[2]);
    }

    protected function parseDateTimeOfLine(string $line): ?\DateTime {
        $res = preg_match('/(\w{3})\s+(\d{2})\s+(\d{2}\:\d{2}\:\d{2})\.\d+\s+(\d{4})/', $line, $matches);
        $month_mapping = [
            'jan' => '01',
            'feb' => '02',
            'mar' => '03',
            'apr' => '04',
            'may' => '05',
            'jun' => '06',
            'jul' => '07',
            'aug' => '08',
            'sep' => '09',
            'oct' => '10',
            'nov' => '11',
            'dec' => '12',
        ];
        if (!$res) {
            return null;
        }
        try {
            $month = $month_mapping[strtolower($matches[1])];
            $date = "{$matches[4]}-{$month}-{$matches[2]}";
            return new \DateTime("{$date} {$matches[3]}");
        } catch (\Throwable $th) {
            return null;
        }
    }
}
