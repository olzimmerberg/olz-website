<?php

namespace Olz\News\Utils;

use Doctrine\Common\Collections\Criteria;
use Olz\Utils\WithUtilsTrait;

class NewsFilterUtils {
    use WithUtilsTrait;

    public const ARCHIVE_YEARS_THRESHOLD = 4;

    public const ALL_FORMAT_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle"],
        ['ident' => 'aktuell', 'name' => "Aktuell", 'icon' => 'entry_type_aktuell_20.svg'],
        ['ident' => 'kaderblog', 'name' => "Kaderblog", 'icon' => 'entry_type_kaderblog_20.svg'],
        ['ident' => 'forum', 'name' => "Forum", 'icon' => 'entry_type_forum_20.svg'],
        ['ident' => 'galerie', 'name' => "Galerien", 'icon' => 'entry_type_gallery_20.svg'],
        ['ident' => 'video', 'name' => "Videos", 'icon' => 'entry_type_movie_20.svg'],
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
                'icon' => $format_option['icon'] ?? null,
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
            return "YEAR(n.published_date) = '{$sane_year}'";
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
            return "n.format LIKE '%aktuell%'";
        }
        if ($filter['format'] === 'kaderblog') {
            return "n.format LIKE '%kaderblog%'";
        }
        if ($filter['format'] === 'forum') {
            return "n.format LIKE '%forum%'";
        }
        if ($filter['format'] === 'galerie') {
            return "n.format LIKE '%galerie%'";
        }
        if ($filter['format'] === 'video') {
            return "n.format LIKE '%video%'";
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
        $archive_title_suffix = $this->getArchiveFilterTitleSuffix($filter);
        $this_year = $this->dateUtils()->getCurrentDateInFormat('Y');
        if ($filter['datum'] === $this_year) {
            $format_title = $this->getPresentFormatFilterTitle($filter);
            return "{$format_title}{$archive_title_suffix}";
        }
        $format_title = $this->getPastFormatFilterTitle($filter);
        if (intval($filter['datum']) > 2000) {
            $year = $filter['datum'];
            return "{$format_title} {$year}{$archive_title_suffix}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "Aktuell{$archive_title_suffix}";
        // @codeCoverageIgnoreEnd
    }

    private function getPresentFormatFilterTitle($filter) {
        if ($filter['format'] === 'aktuell') {
            return "Aktuell";
        }
        if ($filter['format'] === 'kaderblog') {
            return "Kaderblog";
        }
        if ($filter['format'] === 'forum') {
            return "Forum";
        }
        if ($filter['format'] === 'galerie') {
            return "Galerien";
        }
        if ($filter['format'] === 'video') {
            return "Videos";
        }
        return "News";
    }

    private function getPastFormatFilterTitle($filter) {
        if ($filter['format'] === 'aktuell') {
            return "Aktuelles von";
        }
        if ($filter['format'] === 'kaderblog') {
            return "Kaderblog von";
        }
        if ($filter['format'] === 'forum') {
            return "Forumseinträge von";
        }
        if ($filter['format'] === 'galerie') {
            return "Galerien von";
        }
        if ($filter['format'] === 'video') {
            return "Videos von";
        }
        return "News von";
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
        return Criteria::expr()->gte('published_date', new \DateTime($beginning_of_years_ago));
    }

    public function getUrl($filter_override = []) {
        $code_href = $this->envUtils()->getCodeHref();

        $default_filter = $this->getDefaultFilter();
        $filter = [
            ...$default_filter,
            ...$filter_override,
        ];
        $enc_json_filter = urlencode(json_encode($filter));
        return "{$code_href}news?filter={$enc_json_filter}";
    }

    public static function fromEnv(): self {
        return new self();
    }
}
