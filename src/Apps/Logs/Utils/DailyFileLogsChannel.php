<?php

namespace Olz\Apps\Logs\Utils;

abstract class DailyFileLogsChannel extends BaseLogsChannel {
    abstract protected function getRetentionDays(): ?int;

    abstract protected function getLogFileForDateTime(\DateTime $date_time): LogFileInterface;

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
                $date_time_at_index = $this->parseDateTimeOfLine($line ?? '');
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

    public function cleanUpOldFiles(?int $num_days = 30): void {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $interval = "-{$this->getRetentionDays()} days";
        $minus_retention = \DateInterval::createFromDateString($interval);
        $this->generalUtils()->checkNotFalse($minus_retention, "Invalid date interval {$interval}");
        $before_retention = $now->add($minus_retention);

        $day = $before_retention;
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $this->log()->info("Clean up {$num_days} log files before {$day->format('Y-m-d')} in channel {$this->getName()} ({$this->getId()})...");
        for ($i = 0; $i < $num_days; $i++) {
            $day = $day->add($minus_one_day);
            $log_file = $this->getLogFileForDateTime($day);
            $log_file->purge();
        }
    }

    public function optimizeHybridFiles(): void {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $retention_days = $this->getRetentionDays();

        $this->log()->info("Optimizing last {$retention_days} hybrid log files in channel {$this->getName()} ({$this->getId()})...");
        $day = $now;
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        for ($i = 0; $i <= $retention_days; $i++) {
            $log_file = $this->getLogFileForDateTime($day);
            $class_name = get_class($log_file);
            $this->log()->debug("Optimizing {$class_name} for day {$day->format('Y-m-d')}");
            $log_file->optimize();
            $day = $day->add($minus_one_day);
        }
    }
}
