<?php

abstract class AbstractDateUtils {
    const WEEKDAYS_SHORT_DE = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
    const WEEKDAYS_LONG_DE = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
    const MONTHS_SHORT_DE = ["Jan.", "Feb.", "März", "April", "Mai", "Juni", "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez."];
    const MONTHS_LONG_DE = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

    abstract public function getCurrentDateInFormat($format);

    public function getIsoToday() {
        return $this->getCurrentDateInFormat('Y-m-d');
    }

    public function getIsoNow() {
        return $this->getCurrentDateInFormat('Y-m-d H:i:s');
    }

    public function olzDate($format, $date = null) {
        if ($date == null || $date == '') {
            $date = $this->getIsoNow();
        }
        if ($date instanceof DateTime) {
            $date = $date->format(DateTime::ATOM);
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
}
