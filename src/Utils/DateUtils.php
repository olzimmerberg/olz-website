<?php

namespace Olz\Utils;

class DateUtils {
    use WithUtilsTrait;

    public const WEEKDAYS_SHORT_DE = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
    public const WEEKDAYS_LONG_DE = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
    public const MONTHS_SHORT_DE = ["Jan.", "Feb.", "März", "April", "Mai", "Juni", "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez."];
    public const MONTHS_LONG_DE = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

    public function __construct(protected ?string $date = null) {
    }

    public function getCurrentDateInFormat(string $format): string {
        if ($this->date !== null) {
            if ($this->date === 'live') {
                return date($format);
            }
            return date($format, @strtotime($this->date) ?: null);
        }
        $class_name = $this->envUtils()->getDateUtilsClassName();
        $class_args = $this->envUtils()->getDateUtilsClassArgs();

        if ($class_name == 'FixedDateUtils') {
            $fixed_date = $class_args[0];
            $fixed_date = is_int($fixed_date)
                ? $fixed_date
                : (@strtotime($fixed_date) ?: null);
            return date($format, $fixed_date);
        }
        if ($class_name == 'LiveDateUtils') {
            return date($format);
        }
        throw new \Exception("Date class must be FixedDateUtils or LiveDateUtils, was: {$class_name}");
    }

    public function getIsoToday(): string {
        return $this->getCurrentDateInFormat('Y-m-d');
    }

    public function sanitizeDatetimeValue(string|\DateTime|null $value): ?\DateTime {
        if ($value == null) {
            return null;
        }
        if ($value instanceof \DateTime) {
            return $value;
        }
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

    public function sanitizeDateValue(string|\DateTime|null $value): ?\DateTime {
        if ($value == null) {
            return null;
        }
        if ($value instanceof \DateTime) {
            return $value;
        }
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

    public function getIsoNow(): string {
        return $this->getCurrentDateInFormat('Y-m-d H:i:s');
    }

    public function isoDateTime(string|\DateTime|null $date = null): string {
        $timestamp = $this->getTimestamp($date);
        return date("Y-m-d H:i:s", $timestamp);
    }

    public function isoDate(string|\DateTime|null $date = null): string {
        $timestamp = $this->getTimestamp($date);
        return date("Y-m-d", $timestamp);
    }

    public function compactDate(string|\DateTime|null $date = null): string {
        return $this->olzDate("W,\xc2\xa0tt.mm.", $date);
    }

    public function compactTime(string|\DateTime|null $date = null): string {
        $timestamp = $this->getTimestamp($date);
        return date("H:i", $timestamp);
    }

    public function olzDate(string $format, string|\DateTime|null $date = null): string {
        $date = $this->getTimestamp($date);

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
            $format
        );
    }

    protected function getTimestamp(string|\DateTime|null $date = null): int {
        if ($date == null || $date == '') {
            $date = $this->getIsoNow();
        }
        if ($date instanceof \DateTime) {
            $date = $date->format(\DateTime::ATOM);
        }
        $timestamp = strtotime($date);
        $this->generalUtils()->checkNotFalse($timestamp, "No timestamp for {$date}");
        return $timestamp;
    }

    public function formatDateTimeRange(
        string $start_date,
        ?string $start_time,
        ?string $end_date,
        ?string $end_time,
        string $format = 'long',
    ): string {
        if (!$end_date) {
            $end_date = $start_date;
        }
        $out = '';
        // Date
        if ($end_date == $start_date) {
            // Eintägig
            $out = $this->olzDate('WW, t. MM jjjj', $start_date);
        } else {
            $weekday_prefix = $this->olzDate('WW', $start_date).' – '.$this->olzDate('WW', $end_date).', ';
            if ($this->olzDate('m', $start_date) == $this->olzDate('m', $end_date)) {
                // Mehrtägig, innerhalb Monat
                $out = $weekday_prefix.$this->olzDate('t.', $start_date).' – '.$this->olzDate('t. ', $end_date).$this->olzDate('MM jjjj', $start_date);
            } elseif ($this->olzDate('jjjj', $start_date) == $this->olzDate('jjjj', $end_date)) {
                // Mehrtägig, innerhalb Jahr
                $out = $weekday_prefix.$this->olzDate('t. MM', $start_date).' – '.$this->olzDate('t. MM jjjj', $end_date);
            } else {
                // Mehrtägig, jahresübergreifend
                $out = $weekday_prefix.$this->olzDate('t. MM jjjj', $start_date).' – '.$this->olzDate('t. MM jjjj', $end_date);
            }
        }
        // Time
        if ($start_time) {
            $out .= ' '.date('H:i', strtotime($start_time) ?: null);
            if ($end_time) {
                $out .= ' – '.date('H:i', strtotime($end_time) ?: null);
            }
        }
        return $out;
    }
}
