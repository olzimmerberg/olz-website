<?php

class TermineUtils {
    private $date_utils;

    const ALL_TYPE_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle Termine"],
        ['ident' => 'training', 'name' => "Trainings"],
        ['ident' => 'ol', 'name' => "Wettkämpfe"],
        ['ident' => 'club', 'name' => "Vereinsanlässe"],
    ];
    const DEFAULT_FILTER = [
        'typ' => 'alle',
        'datum' => 'bevorstehend',
    ];

    public function setDateUtils($date_utils) {
        $this->date_utils = $date_utils;
    }

    public function isValidFilter($filter) {
        $has_correct_type = (
            isset($filter['typ'])
            && array_filter(
                TermineUtils::ALL_TYPE_OPTIONS,
                function ($type_option) use ($filter) {
                    return $type_option['ident'] === $filter['typ'];
                }
            )
        );
        $has_correct_date_range = (
            isset($filter['datum'])
            && array_filter(
                $this->getDateRangeOptions(),
                function ($type_option) use ($filter) {
                    return $type_option['ident'] === $filter['datum'];
                }
            )
        );
        return $has_correct_type && $has_correct_date_range;
    }

    public function getAllValidFilters() {
        $all_valid_filters = [];
        foreach (TermineUtils::ALL_TYPE_OPTIONS as $type_option) {
            foreach ($this->getDateRangeOptions() as $date_range_option) {
                $all_valid_filters[] = [
                    'typ' => $type_option['ident'],
                    'datum' => $date_range_option['ident'],
                ];
            }
        }
        return $all_valid_filters;
    }

    public function getUiTypeFilterOptions($filter) {
        return array_map(function ($type_option) use ($filter) {
            $new_filter = $filter;
            $new_filter['typ'] = $type_option['ident'];
            return [
                'selected' => $type_option['ident'] === $filter['typ'],
                'new_filter' => $new_filter,
                'name' => $type_option['name'],
                'ident' => $type_option['ident'],
            ];
        }, TermineUtils::ALL_TYPE_OPTIONS);
    }

    public function getUiDateRangeFilterOptions($filter) {
        return array_map(function ($date_range_option) use ($filter) {
            $new_filter = $filter;
            $new_filter['datum'] = $date_range_option['ident'];
            return [
                'selected' => $date_range_option['ident'] === $filter['datum'],
                'new_filter' => $new_filter,
                'name' => $date_range_option['name'],
                'ident' => $date_range_option['ident'],
            ];
        }, $this->getDateRangeOptions());
    }

    public function getDateRangeOptions() {
        $current_year = intval($this->date_utils->getCurrentDateInFormat('Y'));
        $last_year_ident = strval($current_year - 1);
        $this_year_ident = strval($current_year);
        $next_year_ident = strval($current_year + 1);
        return [
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
            ['ident' => $last_year_ident, 'name' => $last_year_ident],
            ['ident' => $this_year_ident, 'name' => $this_year_ident],
            ['ident' => $next_year_ident, 'name' => $next_year_ident],
        ];
    }

    public function getSqlFromFilter($filter) {
        if (!$this->isValidFilter($filter)) {
            return "'1'='0'";
        }
        $date_range_filter = $this->getSqlDateRangeFilter($filter);
        $type_filter = $this->getSqlTypeFilter($filter);
        return "({$date_range_filter}) AND ({$type_filter})";
    }

    private function getSqlDateRangeFilter($filter) {
        $today = $this->date_utils->getIsoToday();
        if ($filter['datum'] === 'bevorstehend') {
            return "(t.datum >= '{$today}') OR (t.datum_end >= '{$today}')";
        }
        if (intval($filter['datum']) > 2000) {
            $sane_year = strval(intval($filter['datum']));
            return "YEAR(t.datum) = '{$sane_year}'";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    private function getSqlTypeFilter($filter) {
        if ($filter['typ'] === 'alle') {
            return "'1' = '1'";
        }
        if ($filter['typ'] === 'training') {
            return "t.typ LIKE '%training%'";
        }
        if ($filter['typ'] === 'ol') {
            return "t.typ LIKE '%ol%'";
        }
        if ($filter['typ'] === 'club') {
            return "t.typ LIKE '%club%'";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    public function getTitleFromFilter($filter) {
        if (!$this->isValidFilter($filter)) {
            return "Termine";
        }
        $type_title = $this->getTypeFilterTitle($filter);
        if ($filter['datum'] == 'bevorstehend') {
            return "Bevorstehende {$type_title}";
        }
        if (intval($filter['datum']) > 2000) {
            $year = $filter['datum'];
            return "{$type_title} {$year}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "Termine";
        // @codeCoverageIgnoreEnd
    }

    private function getTypeFilterTitle($filter) {
        if ($filter['typ'] === 'training') {
            return "Trainings";
        }
        if ($filter['typ'] === 'ol') {
            return "Wettkämpfe";
        }
        if ($filter['typ'] === 'club') {
            return "Vereinsanlässe";
        }
        return "Termine";
    }

    public static function fromEnv() {
        global $_DATE, $_CONFIG;
        require_once __DIR__.'/../config/date.php';
        $termine_utils = new self();
        $termine_utils->setDateUtils($_DATE);
        return $termine_utils;
    }
}
