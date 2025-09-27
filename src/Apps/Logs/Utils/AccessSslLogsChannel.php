<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class AccessSslLogsChannel extends LogrotateLogsChannel {
    use WithUtilsTrait;

    public static function getId(): string {
        return 'access-ssl-logs';
    }

    public static function getName(): string {
        return "Access SSL Logs";
    }

    protected function getLogFileForIndex(int $index): LogFileInterface {
        $log_name = 'access_ssl_log';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $base_index = $index - 1;
        $basename = "{$log_name}.processed.{$base_index}";
        $file_path = "{$syslog_path}{$basename}";
        if ($index === 0) {
            $file_path = "{$syslog_path}{$log_name}";
        }
        if ($index === 1) {
            $file_path = "{$syslog_path}{$log_name}.processed";
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
        $log_name = 'access_ssl_log';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $esc_syslog_path = preg_quote($syslog_path, '/');
        $pattern = "/^{$esc_syslog_path}{$log_name}($|\\.processed$|\\.processed\\.(\\d+)$)/";
        $res = preg_match($pattern, $file_path, $matches);
        if (!$res) {
            throw new \Exception("Not an OLZ Log file path: {$file_path}");
        }
        if ($matches[1] === '') {
            return 0;
        }
        if ($matches[1] === '.processed') {
            return 1;
        }
        $index = intval($matches[2]) + 1;
        if ($index < 0) {
            throw new \Exception("Index is below 0. This should never happen, due to the regex.");
        }
        return $index;
    }

    protected function parseDateTimeOfLine(string $line): ?\DateTime {
        $res = preg_match('/(\d{2})\/(\w{3})\/(\d{4})(:|T|\s+)(\d{2}\:\d{2}\:\d{2})/', $line, $matches);
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
            $month = $month_mapping[strtolower($matches[2])];
            $date = "{$matches[3]}-{$month}-{$matches[1]}";
            return new \DateTime("{$date} {$matches[5]}");
        } catch (\Throwable $th) {
            return null;
        }
    }
}
