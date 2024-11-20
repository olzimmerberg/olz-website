<?php

namespace Olz\Apps\Logs\Utils;

abstract class DailyFileLogsChannel extends BaseLogsChannel {
    abstract protected function getLogFileForDateTime(\DateTime $date_time): PlainLogFile;

    abstract protected function getDateTimeForFilePath(string $file_path): \DateTime;

    protected function getLineLocationForDateTime(
        \DateTime $date_time,
    ): LineLocation {
        $log_file = $this->getLogFileForDateTime($date_time);
        $file_index = $this->getOrCreateIndex($log_file);
        $number_of_lines = count($file_index['lines']);
        $fp = $log_file->open('r');

        [$line_number, $cmp] = $this->generalUtils()->binarySearch(
            function ($line_number) use ($log_file, $fp, $file_index, $date_time) {
                $index = $file_index['lines'][$line_number];
                $log_file->seek($fp, $index);
                $line = $log_file->gets($fp);
                $date_time_at_index = $this->parseDateTimeOfLine($line);
                return $date_time <=> $date_time_at_index;
            },
            0,
            $number_of_lines - 1,
        );

        $log_file->close($fp);
        return new LineLocation($log_file, $line_number, $cmp);
    }

    protected function getLogFileBefore(LogFileInterface $log_file): LogFileInterface {
        $path = $log_file->getPath();
        $date_time = $this->getDateTimeForFilePath($path);
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $iso_noon = $date_time->format('Y-m-d').' 12:00:00';
        $day_before = (new \DateTime($iso_noon))->add($minus_one_day);
        $new_log_file = $this->getLogFileForDateTime($day_before);
        if (!$new_log_file->exists()) {
            throw new \Exception("No such file: {$new_log_file->getPath()}");
        }
        return $new_log_file;
    }

    protected function getLogFileAfter(LogFileInterface $log_file): LogFileInterface {
        $path = $log_file->getPath();
        $date_time = $this->getDateTimeForFilePath($path);
        $plus_one_day = \DateInterval::createFromDateString("+1 days");
        $iso_noon = $date_time->format('Y-m-d').' 12:00:00';
        $day_after = (new \DateTime($iso_noon))->add($plus_one_day);
        $new_log_file = $this->getLogFileForDateTime($day_after);
        if (!$new_log_file->exists()) {
            throw new \Exception("No such file: {$new_log_file->getPath()}");
        }
        return $new_log_file;
    }
}
