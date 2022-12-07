<?php

namespace Olz\News\Utils;

use Doctrine\Common\Collections\Criteria;
use Olz\Utils\WithUtilsTrait;

class NewsFilterUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'dateUtils',
    ];

    public const ARCHIVE_YEARS_THRESHOLD = 4;

    public const ALL_FORMAT_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle"],
        ['ident' => 'aktuell', 'name' => "Aktuell"],
        ['ident' => 'galerie', 'name' => "Galerien"],
        ['ident' => 'video', 'name' => "Videos"],
        // ['ident' => 'kaderblog', 'name' => "Kaderblog"],
        // ['ident' => 'forum', 'name' => "Forum"],
    ];

    public const ALL_ARCHIVE_OPTIONS = [
        ['ident' => 'ohne', 'name' => "ohne Archiv"],
        ['ident' => 'mit', 'name' => "mit Archiv"],
    ];

    public function getDefaultFilter() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        return [
            'format' => 'alle',
            'datum' => strval($current_year),
            'archiv' => 'ohne',
        ];
    }

    public function isValidFilter($filter) {
        $has_correct_format = (
            isset($filter['format'])
            && array_filter(
                NewsFilterUtils::ALL_FORMAT_OPTIONS,
                function ($format_option) use ($filter) {
                    return $format_option['ident'] === $filter['format'];
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
        return $has_correct_format && $has_correct_date_range && $has_correct_archive;
    }

    public function getAllValidFiltersForSitemap() {
        $all_valid_filters = [];
        foreach (NewsFilterUtils::ALL_FORMAT_OPTIONS as $format_option) {
            $date_range_options = $this->getDateRangeOptions(['archiv' => 'ohne']);
            foreach ($date_range_options as $date_range_option) {
                $all_valid_filters[] = [
                    'format' => $format_option['ident'],
                    'datum' => $date_range_option['ident'],
                    'archiv' => 'ohne',
                ];
            }
        }
        return $all_valid_filters;
    }

    public function getUiFormatFilterOptions($filter) {
        return array_map(function ($format_option) use ($filter) {
            $new_filter = $filter;
            $new_filter['format'] = $format_option['ident'];
            return [
                'selected' => $format_option['ident'] === $filter['format'],
                'new_filter' => $new_filter,
                'name' => $format_option['name'],
                'ident' => $format_option['ident'],
            ];
        }, NewsFilterUtils::ALL_FORMAT_OPTIONS);
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
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
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
        $format_filter = $this->getSqlFormatFilter($filter);
        return "({$date_range_filter}) AND ({$format_filter})";
    }

    private function getSqlDateRangeFilter($filter) {
        $today = $this->dateUtils()->getIsoToday();
        if (intval($filter['datum']) > 2000) {
            $sane_year = strval(intval($filter['datum']));
            return "YEAR(n.datum) = '{$sane_year}'";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    private function getSqlFormatFilter($filter) {
        if ($filter['format'] === 'alle') {
            return "'1' = '1'";
        }
        if ($filter['format'] === 'aktuell') {
            return "n.typ LIKE '%aktuell%'";
        }
        if ($filter['format'] === 'galerie') {
            return "n.typ LIKE '%galerie%'";
        }
        if ($filter['format'] === 'video') {
            return "n.typ LIKE '%video%'";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    public function getTitleFromFilter($filter) {
        if (!$this->isValidFilter($filter)) {
            return "Aktuell";
        }
        $format_title = $this->getFormatFilterTitle($filter);
        $archive_title_suffix = $this->getArchiveFilterTitleSuffix($filter);
        if (intval($filter['datum']) > 2000) {
            $year = $filter['datum'];
            return "{$format_title} {$year}{$archive_title_suffix}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "Aktuell{$archive_title_suffix}";
        // @codeCoverageIgnoreEnd
    }

    private function getFormatFilterTitle($filter) {
        if ($filter['format'] === 'aktuell') {
            return "Aktuelles von";
        }
        if ($filter['format'] === 'galerie') {
            return "Galerien von";
        }
        if ($filter['format'] === 'video') {
            return "Videos von";
        }
        return "Alles von";
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
        $years_ago = $this->dateUtils()->getCurrentDateInFormat('Y') - NewsFilterUtils::ARCHIVE_YEARS_THRESHOLD;
        $beginning_of_years_ago = "{$years_ago}-01-01";
        return Criteria::expr()->gte('datum', new \DateTime($beginning_of_years_ago));
    }
}
