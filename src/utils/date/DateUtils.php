<?php

abstract class DateUtils {
    public $weekdays_short_de = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
    public $weekdays_long_de = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
    public $months_short_de = ["Jan.", "Feb.", "März", "April", "Mai", "Juni", "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez."];
    public $months_long_de = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

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
                $this->months_short_de[date("n", $date) - 1],
                strftime("%B", $date),
                date("Y", $date),
                date("y", $date),
                date("w", $date),
                $this->weekdays_long_de[date("w", $date)],
                $this->weekdays_short_de[date("w", $date)],
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
