<?php

namespace Olz\Utils;

abstract class AbstractDateUtils {
    public const UTILS = [];

    public const WEEKDAYS_SHORT_DE = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
    public const WEEKDAYS_LONG_DE = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
    public const MONTHS_SHORT_DE = ["Jan.", "Feb.", "März", "April", "Mai", "Juni", "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez."];
    public const MONTHS_LONG_DE = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

    abstract public function getCurrentDateInFormat($format);

    public function getIsoToday() {
        return $this->getCurrentDateInFormat('Y-m-d');
    }

    public function sanitizeDatetimeValue($value) {
        if ($value == null) {
            return null;
        }
        if ($value instanceof \DateTime) {
            return $value;
        }
        if (is_string($value)) {
            $res = preg_match('/[0-9]+\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $value);
            if (!$res) {
                throw new \Exception("Invalid datetime: {$value}", 1);
            }
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
            if (!$datetime) {
                throw new \Exception("Invalid datetime: {$value}", 1);
            }
            return $datetime;
        }
        throw new \Exception("Invalid datetime: {$value}", 1);
    }

    public function sanitizeDateValue($value) {
        if ($value == null) {
            return null;
        }
        if ($value instanceof \DateTime) {
            return $value;
        }
        if (is_string($value)) {
            $res = preg_match('/[0-9]+\-[0-9]{2}\-[0-9]{2}/', $value);
            if (!$res) {
                throw new \Exception("Invalid datetime: {$value}", 1);
            }
            $datetime = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$datetime) {
                throw new \Exception("Invalid datetime: {$value}", 1);
            }
            return $datetime;
        }
        throw new \Exception("Invalid datetime: {$value}", 1);
    }

    public function getIsoNow() {
        return $this->getCurrentDateInFormat('Y-m-d H:i:s');
    }

    public function olzDate($format, $date = null) {
        if ($date == null || $date == '') {
            $date = $this->getIsoNow();
        }
        if ($date instanceof \DateTime) {
            $date = $date->format(\DateTime::ATOM);
        }
        if (is_string($date)) {
            $date = strtotime($date);
        }

        return str_replace(
            [
                "tt",
                "t",
                "mm",
                "m",
                "MM",
                "M",
                "xxxxx",
                "jjjj",
                "jj",
                "w",
                "WW",
                "W",
            ],
            [
                date("d", $date),
                date("j", $date),
                date("m", $date),
                date("n", $date),
                "xxxxx",
                self::MONTHS_SHORT_DE[date("n", $date) - 1],
                self::MONTHS_LONG_DE[date("n", $date) - 1],
                date("Y", $date),
                date("y", $date),
                date("w", $date),
                self::WEEKDAYS_LONG_DE[date("w", $date)],
                self::WEEKDAYS_SHORT_DE[date("w", $date)],
            ],
            $format);
    }

    public function getYearsForAccordion() {
        global $_GET, $_SESSION;
        $this_year = intval($this->getCurrentDateInFormat('Y'));
        $latest_year = isset($_SESSION['auth']) ? $this_year + 1 : $this_year;
        $earliest_year = isset($_GET['archiv']) ? 2005 : $this_year - 5;
        $years = [];
        for ($year = $latest_year; $earliest_year <= $year; $year--) {
            array_push($years, $year);
        }
        return $years;
    }

    public static function fromEnv() {
        $env_utils = EnvUtils::fromEnv();
        $class_name = $env_utils->getDateUtilsClassName();
        $class_args = $env_utils->getDateUtilsClassArgs();

        if ($class_name == 'FixedDateUtils') {
            return new FixedDateUtils($class_args[0]);
        }
        if ($class_name == 'LiveDateUtils') {
            return new LiveDateUtils();
        }
        throw new \Exception("Invalid DateUtils class name: {$class_name}");
    }
}
