<?php

use Doctrine\Common\Collections\Criteria;

class TermineFilterUtils {
    private $date_utils;

    public const ARCHIVE_YEARS_THRESHOLD = 4;

    public const ALL_TYPE_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle Termine"],
        ['ident' => 'programm', 'name' => "Jahresprogramm"],
        ['ident' => 'weekend', 'name' => "Weekends"],
        ['ident' => 'training', 'name' => "Trainings"],
        ['ident' => 'ol', 'name' => "Wettkämpfe"],
        ['ident' => 'club', 'name' => "Vereinsanlässe"],
    ];

    public const ALL_ARCHIVE_OPTIONS = [
        ['ident' => 'ohne', 'name' => "ohne Archiv"],
        ['ident' => 'mit', 'name' => "mit Archiv"],
    ];

    public function setDateUtils($date_utils) {
        $this->date_utils = $date_utils;
    }

    public function getDefaultFilter() {
        return [
            'typ' => 'alle',
            'datum' => 'bevorstehend',
            'archiv' => 'ohne',
        ];
    }

    public function isValidFilter($filter) {
        $has_correct_type = (
            isset($filter['typ'])
            && array_filter(
                TermineFilterUtils::ALL_TYPE_OPTIONS,
                function ($type_option) use ($filter) {
                    return $type_option['ident'] === $filter['typ'];
                }
            )
        );
        $has_correct_date_range = (
            isset($filter['datum'])
            && array_filter(
                $this->getDateRangeOptions($filter),
                function ($type_option) use ($filter) {
                    return $type_option['ident'] === $filter['datum'];
                }
            )
        );
        $has_correct_archive = (
            isset($filter['archiv'])
            && array_filter(
                TermineFilterUtils::ALL_ARCHIVE_OPTIONS,
                function ($archive_option) use ($filter) {
                    return $archive_option['ident'] === $filter['archiv'];
                }
            )
        );
        return $has_correct_type && $has_correct_date_range && $has_correct_archive;
    }

    public function getAllValidFiltersForSitemap() {
        $all_valid_filters = [];
        foreach (TermineFilterUtils::ALL_TYPE_OPTIONS as $type_option) {
            $date_range_options = $this->getDateRangeOptions(['archiv' => 'ohne']);
            foreach ($date_range_options as $date_range_option) {
                $all_valid_filters[] = [
                    'typ' => $type_option['ident'],
                    'datum' => $date_range_option['ident'],
                    'archiv' => 'ohne',
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
        }, TermineFilterUtils::ALL_TYPE_OPTIONS);
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
        }, $this->getDateRangeOptions($filter));
    }

    public function getUiArchiveFilterOptions($filter) {
        return array_map(function ($archive_option) use ($filter) {
            $new_filter = $filter;
            $new_filter['archiv'] = $archive_option['ident'];
            return [
                'selected' => $archive_option['ident'] === $filter['archiv'],
                'new_filter' => $new_filter,
                'name' => $archive_option['name'],
                'ident' => $archive_option['ident'],
            ];
        }, TermineFilterUtils::ALL_ARCHIVE_OPTIONS);
    }

    public function getDateRangeOptions($filter = []) {
        $include_archive = ($filter['archiv'] ?? null) === 'mit';
        $current_year = intval($this->date_utils->getCurrentDateInFormat('Y'));
        $first_year = $include_archive ? 2006 : $current_year - TermineFilterUtils::ARCHIVE_YEARS_THRESHOLD;
        $options = [
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
        ];
        for ($year = $current_year + 1; $year >= $first_year; $year--) {
            $year_ident = strval($year);
            $options[] = ['ident' => $year_ident, 'name' => $year_ident];
        }
        return $options;
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
        if ($filter['typ'] === 'programm') {
            return "t.typ LIKE '%programm%'";
        }
        if ($filter['typ'] === 'weekend') {
            return "t.typ LIKE '%weekend%'";
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
        $archive_title_suffix = $this->getArchiveFilterTitleSuffix($filter);
        $year_suffix = $this->getDateFilterTitleYearSuffix($filter);
        $is_upcoming = $filter['datum'] == 'bevorstehend';
        if ($filter['typ'] === 'alle') {
            if ($is_upcoming) {
                return "Bevorstehende Termine{$archive_title_suffix}";
            }
            return "Termine{$year_suffix}{$archive_title_suffix}";
        }
        if ($filter['typ'] === 'programm') {
            if ($is_upcoming) {
                return "Bevorstehendes Jahresprogramm{$archive_title_suffix}";
            }
            return "Jahresprogramm{$year_suffix}{$archive_title_suffix}";
        }
        if ($filter['typ'] === 'weekend') {
            if ($is_upcoming) {
                return "Bevorstehende Weekends{$archive_title_suffix}";
            }
            return "Weekends{$year_suffix}{$archive_title_suffix}";
        }
        if ($filter['typ'] === 'training') {
            if ($is_upcoming) {
                return "Bevorstehende Trainings{$archive_title_suffix}";
            }
            return "Trainingsplan{$year_suffix}{$archive_title_suffix}";
        }
        if ($filter['typ'] === 'ol') {
            if ($is_upcoming) {
                return "Bevorstehende Wettkämpfe{$archive_title_suffix}";
            }
            return "Wettkämpfe{$year_suffix}{$archive_title_suffix}";
        }
        if ($filter['typ'] === 'club') {
            if ($is_upcoming) {
                return "Bevorstehende Vereinsanlässe{$archive_title_suffix}";
            }
            return "Vereinsanlässe{$year_suffix}{$archive_title_suffix}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "Termine{$archive_title_suffix}";
        // @codeCoverageIgnoreEnd
    }

    private function getDateFilterTitleYearSuffix($filter) {
        if ($filter['datum'] == 'bevorstehend') {
            return "";
        }
        if (intval($filter['datum']) < 2000) {
            // @codeCoverageIgnoreStart
            // Reason: Should not be reached.
            // TODO: Logging
            return "";
            // @codeCoverageIgnoreEnd
        }
        $year = $filter['datum'];
        return " {$year}";
    }

    private function getArchiveFilterTitleSuffix($filter) {
        if ($filter['archiv'] === 'mit') {
            return " (Archiv)";
        }
        return "";
    }

    public function isFilterNotArchived($filter) {
        return ($filter['archiv'] ?? null) === 'ohne';
    }

    public function getIsNotArchivedCriteria() {
        $years_ago = $this->date_utils->getCurrentDateInFormat('Y') - TermineFilterUtils::ARCHIVE_YEARS_THRESHOLD;
        $beginning_of_years_ago = "{$years_ago}-01-01";
        return Criteria::expr()->gte('datum', new DateTime($beginning_of_years_ago));
    }

    public static function fromEnv() {
        require_once __DIR__.'/../../utils/date/DateUtils.php';
        $date_utils = DateUtils::fromEnv();
        $termine_utils = new self();
        $termine_utils->setDateUtils($date_utils);
        return $termine_utils;
    }
}