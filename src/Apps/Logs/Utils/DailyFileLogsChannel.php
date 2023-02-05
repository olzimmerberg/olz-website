<?php

namespace Olz\Apps\Logs\Utils;

abstract class DailyFileLogsChannel extends BaseLogsChannel {
    abstract protected function getFilePathForDateTime(\DateTime $date_time): string;

    abstract protected function getDateTimeForFilePath(string $file_path): \DateTime;

    protected function getFilePathBefore(string $path): string {
        $date_time = $this->getDateTimeForFilePath($path);
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $iso_noon = $date_time->format('Y-m-d').' 12:00:00';
        $day_before = (new \DateTime($iso_noon))->add($minus_one_day);
        $file_path = $this->getFilePathForDateTime($day_before);
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return $file_path;
    }

    protected function getFilePathAfter(string $path): string {
        $date_time = $this->getDateTimeForFilePath($path);
        $plus_one_day = \DateInterval::createFromDateString("+1 days");
        $iso_noon = $date_time->format('Y-m-d').' 12:00:00';
        $day_after = (new \DateTime($iso_noon))->add($plus_one_day);
        $file_path = $this->getFilePathForDateTime($day_after);
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return $file_path;
    }
}
