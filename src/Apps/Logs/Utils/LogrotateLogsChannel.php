<?php

namespace Olz\Apps\Logs\Utils;

abstract class LogrotateLogsChannel extends BaseLogsChannel {
    abstract protected function getLogFileForIndex(int $index): LogFileInterface;

    abstract protected function getIndexForFilePath(string $file_path): int;

    protected function getLineLocationForDateTime(
        \DateTime $date_time,
    ): LineLocation {
        $iso_date_time = $date_time->format('Y-m-d H:i:s');
        $index = -1;
        $continue = true;
        while ($continue) {
            try {
                $log_file = $this->getLogFileForIndex($index);
                $file_index = $this->getOrCreateIndex($log_file);
                if ($iso_date_time >= $file_index['start_date']) {
                    $continue = false;
                } else {
                    $index++;
                }
            } catch (\Exception $exc) {
                $continue = false;
                throw $exc;
            }
        }
        $log_file = $this->getLogFileForIndex($index);
        $file_index = $this->getOrCreateIndex($log_file);
        $number_of_lines = count($file_index['lines']);
        $fp = $log_file->open('r');

        $line_number = $this->generalUtils()->binarySearch(
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
        return new LineLocation($log_file, $line_number);
    }

    protected function getLogFileBefore(LogFileInterface $log_file): LogFileInterface {
        $path = $log_file->getPath();
        $index = $this->getIndexForFilePath($path);
        $index_before = $index + 1;
        $new_log_file = $this->getLogFileForIndex($index_before);
        if (!$new_log_file->exists()) {
            throw new \Exception("No such file: {$new_log_file->getPath()}");
        }
        return $new_log_file;
    }

    protected function getLogFileAfter(LogFileInterface $log_file): LogFileInterface {
        $path = $log_file->getPath();
        $index = $this->getIndexForFilePath($path);
        $index_after = $index - 1;
        $new_log_file = $this->getLogFileForIndex($index_after);
        if (!$new_log_file->exists()) {
            throw new \Exception("No such file: {$new_log_file->getPath()}");
        }
        return $new_log_file;
    }
}
