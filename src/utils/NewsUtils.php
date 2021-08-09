<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/../config/doctrine.php';

class NewsUtils {
    private $date_utils;

    const ALL_TYPE_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle News"],
        ['ident' => 'aktuell', 'name' => "Aktuell"],
        // ['ident' => 'galerie', 'name' => "Galerien"],
        // ['ident' => 'kaderblog', 'name' => "Kaderblog"],
        // ['ident' => 'forum', 'name' => "Forum"],
    ];

    public function setDateUtils($date_utils) {
        $this->date_utils = $date_utils;
    }

    public function getDefaultFilter() {
        $current_year = intval($this->date_utils->getCurrentDateInFormat('Y'));
        return [
            'typ' => 'aktuell',
            'datum' => strval($current_year),
        ];
    }

    public function isValidFilter($filter) {
        $has_correct_type = (
            isset($filter['typ'])
            && array_filter(
                NewsUtils::ALL_TYPE_OPTIONS,
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
        foreach (NewsUtils::ALL_TYPE_OPTIONS as $type_option) {
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
        }, NewsUtils::ALL_TYPE_OPTIONS);
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
        $options = [];
        for ($i = 0; $i < 5; $i++) {
            $year_ident = strval($current_year - $i);
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
        if (intval($filter['datum']) > 2000) {
            $year = $filter['datum'];
            return "{$type_title} {$year}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "News";
        // @codeCoverageIgnoreEnd
    }

    private function getTypeFilterTitle($filter) {
        if ($filter['typ'] === 'aktuell') {
            return "Aktuell";
        }
        return "News";
    }

    public function getIsNewsNotArchivedCriteria() {
        $five_years_ago = $this->date_utils->getCurrentDateInFormat('Y') - 5;
        $beginning_of_five_years_ago = "{$five_years_ago}-01-01";
        return Criteria::expr()->gte('datum', new DateTime($beginning_of_five_years_ago));
    }

    public static function fromEnv() {
        global $_DATE, $_CONFIG;
        require_once __DIR__.'/../config/date.php';
        $termine_utils = new self();
        $termine_utils->setDateUtils($_DATE);
        return $termine_utils;
    }
}
