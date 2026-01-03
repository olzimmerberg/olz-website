<?php

namespace Olz\News\Utils;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Olz\Entity\News\NewsEntry;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsTrait;

/**
 * @phpstan-type Option array{ident: string, name: string, icon?: string}
 * @phpstan-type UiOption array{selected: bool, new_filter: FullFilter, name: string, icon?: ?string, ident: string}
 * @phpstan-type FullFilter array{format: string, datum: string}
 * @phpstan-type PartialFilter array{format?: string, datum?: string}
 */
class NewsUtils {
    use WithUtilsTrait;

    public const ALL_FORMAT_OPTIONS = [
        ['ident' => 'alle', 'name' => "Alle"],
        ['ident' => 'aktuell', 'name' => "Aktuell", 'icon' => 'entry_type_aktuell_20.svg'],
        ['ident' => 'kaderblog', 'name' => "Kaderblog", 'icon' => 'entry_type_kaderblog_20.svg'],
        ['ident' => 'forum', 'name' => "Forum", 'icon' => 'entry_type_forum_20.svg'],
        ['ident' => 'galerie', 'name' => "Galerien", 'icon' => 'entry_type_galerie_20.svg'],
        ['ident' => 'video', 'name' => "Videos", 'icon' => 'entry_type_video_20.svg'],
    ];

    /** @param array<string, string> $filter */
    public function serialize(array $filter): string {
        $json = json_encode($filter) ?: '';
        return str_replace(['{"', '":"', '","', '"}'], ['', '-', '---', ''], $json);
    }

    /** @return ?array<string, string> */
    public function deserialize(string $input): ?array {
        $json = '{"'.str_replace(['---', '-'], ['","', '":"'], $input).'"}';
        return json_decode($json, true) ?? json_decode($input, true);
    }

    /** @return FullFilter */
    public function getDefaultFilter(): array {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        return [
            'format' => 'alle',
            'datum' => strval($current_year),
        ];
    }

    /** @param ?array<string, string> $filter */
    public function isValidFilter(?array $filter): bool {
        $has_correct_format = (
            isset($filter['format'])
            && array_filter(
                NewsUtils::ALL_FORMAT_OPTIONS,
                function ($format_option) use ($filter) {
                    return $format_option['ident'] === $filter['format'];
                }
            )
        );
        $has_correct_date_range = (
            isset($filter['datum'])
            && array_filter(
                $this->getDateRangeOptions(),
                function ($date_option) use ($filter) {
                    return $date_option['ident'] === $filter['datum'];
                }
            )
        );
        $has_no_other_keys = !array_filter(
            array_keys($filter ?? []),
            fn ($key) => $key !== 'format' && $key !== 'datum',
        );
        return $has_correct_format && $has_correct_date_range && $has_no_other_keys;
    }

    /**
     * @param ?PartialFilter $filter
     *
     * @return FullFilter
     */
    public function getValidFilter(?array $filter): array {
        $default_filter = $this->getDefaultFilter();
        if (!$filter) {
            return $default_filter;
        }
        $merged_filter = [
            'format' => $filter['format'] ?? $default_filter['format'],
            'datum' => $filter['datum'] ?? $default_filter['datum'],
        ];
        return $this->isValidFilter($merged_filter) ? $merged_filter : $default_filter;
    }

    /** @return array<FullFilter> */
    public function getAllValidFiltersForSitemap(): array {
        $all_valid_filters = [];
        foreach (NewsUtils::ALL_FORMAT_OPTIONS as $format_option) {
            $date_range_options = $this->getDateRangeOptions();
            foreach ($date_range_options as $date_range_option) {
                $all_valid_filters[] = [
                    'format' => $format_option['ident'],
                    'datum' => $date_range_option['ident'],
                ];
            }
        }
        return $all_valid_filters;
    }

    /**
     * @param FullFilter $filter
     *
     * @return array<UiOption>
     */
    public function getUiFormatFilterOptions(array $filter): array {
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
        }, NewsUtils::ALL_FORMAT_OPTIONS);
    }

    /**
     * @param FullFilter $filter
     *
     * @return array<UiOption>
     */
    public function getUiDateRangeFilterOptions(array $filter): array {
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

    public function hasArchiveAccess(): bool {
        return $this->authUtils()->hasPermission('verified_email');
    }

    /**
     * @return array<Option>
     */
    public function getDateRangeOptions(): array {
        $include_archive = $this->hasArchiveAccess();
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $first_year = $include_archive ? 2006 : $current_year - DateUtils::ARCHIVE_YEARS_THRESHOLD;
        $options = [];
        for ($year = $current_year; $year >= $first_year; $year--) {
            $year_ident = strval($year);
            $options[] = ['ident' => $year_ident, 'name' => $year_ident];
        }
        return $options;
    }

    /** @param FullFilter $filter */
    public function getSqlFromFilter(array $filter): string {
        if (!$this->isValidFilter($filter)) {
            return "'1'='0'";
        }
        $date_range_filter = $this->getSqlDateRangeFilter($filter);
        $format_filter = $this->getSqlFormatFilter($filter);
        return "({$date_range_filter}) AND ({$format_filter})";
    }

    /** @param FullFilter $filter */
    private function getSqlDateRangeFilter(array $filter): string {
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

    /** @param FullFilter $filter */
    private function getSqlFormatFilter(array $filter): string {
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

    /** @param FullFilter $filter */
    public function getTitleFromFilter(array $filter): string {
        if (!$this->isValidFilter($filter)) {
            return "News";
        }
        $this_year = $this->dateUtils()->getCurrentDateInFormat('Y');
        if ($filter['datum'] === $this_year) {
            $format_title = $this->getPresentFormatFilterTitle($filter);
            return "{$format_title}";
        }
        $format_title = $this->getPastFormatFilterTitle($filter);
        if (intval($filter['datum']) > 2000) {
            $year = $filter['datum'];
            return "{$format_title} {$year}";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "Aktuell";
        // @codeCoverageIgnoreEnd
    }

    /** @param FullFilter $filter */
    private function getPresentFormatFilterTitle(array $filter): string {
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

    /** @param FullFilter $filter */
    private function getPastFormatFilterTitle(array $filter): string {
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

    public function getIsNotArchivedCriteria(): Comparison {
        $archive_threshold = $this->dateUtils()->getIsoArchiveThreshold();
        return Criteria::expr()->gte('published_date', new \DateTime($archive_threshold));
    }

    public function getIsNotArchivedSql(?string $tbl = null): string {
        $tbl_sql = $tbl === null ? '' : "{$tbl}.";
        $archive_threshold = $this->dateUtils()->getIsoArchiveThreshold();
        return "{$tbl_sql}published_date >= '{$archive_threshold}'";
    }

    /** @param PartialFilter $filter */
    public function getUrl(array $filter = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $serialized_filter = $this->newsUtils()->serialize($filter);
        return "{$code_href}news?filter={$serialized_filter}&seite=1";
    }

    public function getNewsFormatIcon(
        NewsEntry|string $input,
        ?string $modifier = null,
    ): ?string {
        $format = $input instanceof NewsEntry ? $input->getFormat() : $input;
        $key = $modifier === null ? $format : "{$format}_{$modifier}";
        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();
        $path = "assets/icns/entry_type_{$key}_20.svg";
        return is_file("{$code_path}{$path}") ? "{$code_href}{$path}" : null;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
