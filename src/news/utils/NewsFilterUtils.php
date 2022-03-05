<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/../../config/doctrine.php';

class NewsFilterUtils {
    private $date_utils;

    public const ARCHIVE_YEARS_THRESHOLD = 4;

    public const ALL_TYPE_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle News"],
        ['ident' => 'aktuell', 'name' => "Aktuell"],
        // ['ident' => 'galerie', 'name' => "Galerien"],
        // ['ident' => 'kaderblog', 'name' => "Kaderblog"],
        // ['ident' => 'forum', 'name' => "Forum"],
    ];

    public const ALL_ARCHIVE_OPTIONS = [
        ['ident' => 'ohne', 'name' => "ohne Archiv"],
        ['ident' => 'mit', 'name' => "mit Archiv"],
    ];

    public function setDateUtils($date_utils) {
        $this->date_utils = $date_utils;
    }

    public function getDefaultFilter() {
        $current_year = intval($this->date_utils->getCurrentDateInFormat('Y'));
        return [
            'typ' => 'aktuell',
            'datum' => strval($current_year),
            'archiv' => 'ohne',
        ];
    }

    public function isValidFilter($filter) {
        $has_correct_type = (
            isset($filter['typ'])
            && array_filter(
                NewsFilterUtils::ALL_TYPE_OPTIONS,
                function ($type_option) use ($filter) {
                    return $type_option['ident'] === $filter['typ'];
                }
            )
        );
        $has_correct_date_range = (
            isset($filter['datum'])
            && array_filter(
                $this->getDateRangeOptions($filter),
                function ($date_option) use ($filter) {
                    return $date_option['ident'] === $filter['datum'];
                }
            )
        );
        $has_correct_archive = (
            isset($filter['archiv'])
            && array_filter(
                NewsFilterUtils::ALL_ARCHIVE_OPTIONS,
                function ($archive_option) use ($filter) {
                    return $archive_option['ident'] === $filter['archiv'];
                }
            )
        );
        return $has_correct_type && $has_correct_date_range && $has_correct_archive;
    }

    public function getAllValidFiltersForSitemap() {
        $all_valid_filters = [];
        foreach (NewsFilterUtils::ALL_TYPE_OPTIONS as $type_option) {
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
        }, NewsFilterUtils::ALL_TYPE_OPTIONS);
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
        }, NewsFilterUtils::ALL_ARCHIVE_OPTIONS);
    }

    public function getDateRangeOptions($filter = []) {
        $include_archive = ($filter['archiv'] ?? null) === 'mit';
        $current_year = intval($this->date_utils->getCurrentDateInFormat('Y'));
        $first_year = $include_archive ? 2006 : $current_year - NewsFilterUtils::ARCHIVE_YEARS_THRESHOLD;
        $options = [];
        for ($year = $current_year; $year >= $first_year; $year--) {
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
        if (intval($filter['datum']) > 2000) {
            $sane_year = strval(intval($filter['datum']));
            return "YEAR(n.datum) = '{$sane_year}'";
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
        if ($filter['typ'] === 'aktuell') {
            return "n.typ LIKE '%aktuell%'";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    public function getTitleFromFilter($filter) {
        if (!$this->isValidFilter($filter)) {
            return "News";
        }
        $type_title = $this->getTypeFilterTitle($filter);
        $archive_title_suffix = $this->getArchiveFilterTitleSuffix($filter);
        if (intval($filter['datum']) > 2000) {
            $year = $filter['datum'];
            return "{$type_title} {$year}{$archive_title_suffix}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "News{$archive_title_suffix}";
        // @codeCoverageIgnoreEnd
    }

    private function getTypeFilterTitle($filter) {
        if ($filter['typ'] === 'aktuell') {
            return "Aktuell";
        }
        return "News";
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
        $years_ago = $this->date_utils->getCurrentDateInFormat('Y') - NewsFilterUtils::ARCHIVE_YEARS_THRESHOLD;
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
