<?php

namespace Olz\Termine\Utils;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Olz\Entity\Termine\TerminLabel;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsTrait;

/**
 * @phpstan-type Option array{ident: string, name: string, icon?: string}
 * @phpstan-type UiOption array{selected: bool, new_filter: FullFilter, name: string, icon: ?string, ident: string}
 * @phpstan-type FullFilter array{typ: string, datum: string, archiv: string}
 * @phpstan-type PartialFilter array{typ?: string, datum?: string, archiv?: string}
 */
class TermineFilterUtils {
    use WithUtilsTrait;

    public const ALL_ARCHIVE_OPTIONS = [
        ['ident' => 'ohne', 'name' => "ohne Archiv"],
        ['ident' => 'mit', 'name' => "mit Archiv"],
    ];

    /** @var array<Option> */
    public array $allTypeOptions = [];

    public function loadTypeOptions(): self {
        $code_href = $this->envUtils()->getCodeHref();
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);
        $termine_labels = $termin_label_repo->findBy(['on_off' => 1], ['position' => 'ASC']);
        $this->allTypeOptions = [
            [
                'ident' => 'alle',
                'name' => "Alle Termine",
            ],
            ...array_map(function ($label) use ($code_href) {
                $ident = "{$label->getIdent()}";
                $fallback_href = "{$code_href}assets/icns/termine_type_{$ident}_20.svg";
                return [
                    'ident' => $ident,
                    'name' => "{$label->getName()}",
                    'icon' => $label->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href,
                ];
            }, $termine_labels),
            [
                'ident' => 'meldeschluss',
                'name' => "Meldeschlüsse",
                'icon' => "{$code_href}assets/icns/termine_type_meldeschluss_20.svg",
            ],
        ];
        return $this;
    }

    /** @return FullFilter */
    public function getDefaultFilter(): array {
        return [
            'typ' => 'alle',
            'datum' => 'bevorstehend',
            'archiv' => 'ohne',
        ];
    }

    /** @param ?PartialFilter $filter */
    public function isValidFilter(?array $filter): bool {
        $has_correct_type = (
            isset($filter['typ'])
            && array_filter(
                $this->allTypeOptions,
                function ($type_option) use ($filter) {
                    return $type_option['ident'] === $filter['typ'];
                }
            )
        );
        $has_correct_date_range = (
            isset($filter['datum'])
            && array_filter(
                $this->getDateRangeOptions([...$this->getDefaultFilter(), ...$filter]),
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
            'typ' => $filter['typ'] ?? $default_filter['typ'],
            'datum' => $filter['datum'] ?? $default_filter['datum'],
            'archiv' => $filter['archiv'] ?? $default_filter['archiv'],
        ];
        return $this->isValidFilter($merged_filter) ? $merged_filter : $default_filter;
    }

    /** @return array<FullFilter> */
    public function getAllValidFiltersForSitemap(): array {
        $all_valid_filters = [];
        foreach ($this->allTypeOptions as $type_option) {
            $date_range_options = $this->getDateRangeOptions($this->getValidFilter(['archiv' => 'ohne']));
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

    /**
     * @param FullFilter $filter
     *
     * @return array<UiOption>
     */
    public function getUiTypeFilterOptions(array $filter): array {
        return array_map(function ($type_option) use ($filter) {
            $new_filter = $filter;
            $new_filter['typ'] = $type_option['ident'];
            return [
                'selected' => $type_option['ident'] === $filter['typ'],
                'new_filter' => $new_filter,
                'name' => $type_option['name'],
                'icon' => $type_option['icon'] ?? null,
                'ident' => $type_option['ident'],
            ];
        }, $this->allTypeOptions);
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
                'icon' => null,
                'ident' => $date_range_option['ident'],
            ];
        }, $this->getDateRangeOptions($filter));
    }

    /**
     * @param FullFilter $filter
     *
     * @return array<UiOption>
     */
    public function getUiArchiveFilterOptions(array $filter): array {
        return array_map(function ($archive_option) use ($filter) {
            $new_filter = $filter;
            $new_filter['archiv'] = $archive_option['ident'];
            return [
                'selected' => $archive_option['ident'] === $filter['archiv'],
                'new_filter' => $new_filter,
                'name' => $archive_option['name'],
                'icon' => null,
                'ident' => $archive_option['ident'],
            ];
        }, TermineFilterUtils::ALL_ARCHIVE_OPTIONS);
    }

    /**
     * @param FullFilter $filter
     *
     * @return array<Option>
     */
    public function getDateRangeOptions(array $filter): array {
        $include_archive = $filter['archiv'] === 'mit';
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $first_year = $include_archive ? 2006 : $current_year - DateUtils::ARCHIVE_YEARS_THRESHOLD;
        $options = [
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
        ];
        for ($year = $current_year + 1; $year >= $first_year; $year--) {
            $year_ident = strval($year);
            $options[] = ['ident' => $year_ident, 'name' => $year_ident];
        }
        return $options;
    }

    /** @param PartialFilter $filter_arg */
    public function getSqlDateRangeFilter(array $filter_arg, string $tbl = 't'): string {
        if (!$this->isValidFilter($filter_arg)) {
            return "'1'='0'";
        }
        $filter = $this->getValidFilter($filter_arg);
        $today = $this->dateUtils()->getIsoToday();
        if ($filter['datum'] === 'bevorstehend') {
            return "({$tbl}.start_date >= '{$today}') OR ({$tbl}.end_date >= '{$today}')";
        }
        if (intval($filter['datum']) > 2000) {
            $sane_year = strval(intval($filter['datum']));
            return "YEAR({$tbl}.start_date) = '{$sane_year}'";
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    /** @param PartialFilter $filter_arg */
    public function getSqlTypeFilter(array $filter_arg, string $tbl = 't'): string {
        if (!$this->isValidFilter($filter_arg)) {
            return "'1'='0'";
        }
        $filter = $this->getValidFilter($filter_arg);
        if ($filter['typ'] === 'alle') {
            return "'1' = '1'";
        }
        if ($filter['typ'] === 'meldeschluss') {
            return "{$tbl}.typ LIKE '%meldeschluss%'";
        }
        foreach ($this->allTypeOptions as $type_option) {
            $ident = $type_option['ident'];
            if ($filter['typ'] === $ident && preg_match('/^[a-zA-Z0-9_]+$/', $ident)) {
                return "{$tbl}.typ LIKE '%{$ident}%'";
            }
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "'1' = '0'"; // invalid => show nothing
        // @codeCoverageIgnoreEnd
    }

    /** @param FullFilter $filter */
    public function getTitleFromFilter(array $filter): string {
        if (!$this->isValidFilter($filter)) {
            return "Termine";
        }
        $archive_title_suffix = $this->getArchiveFilterTitleSuffix($filter);
        $year_suffix = $this->getDateFilterTitleYearSuffix($filter);
        $is_upcoming = $filter['datum'] == 'bevorstehend';
        if ($filter['typ'] === 'alle') {
            if ($is_upcoming) {
                return "Bevorstehende Termine";
            }
            return "Termine{$year_suffix}{$archive_title_suffix}";
        }
        if ($filter['typ'] === 'meldeschluss') {
            if ($is_upcoming) {
                return "Bevorstehende Meldeschlüsse";
            }
            return "Meldeschlüsse{$year_suffix}{$archive_title_suffix}";
        }
        foreach ($this->allTypeOptions as $type_option) {
            if ($filter['typ'] === $type_option['ident']) {
                $name = $type_option['name'];
                if ($is_upcoming) {
                    return "{$name} (bevorstehend)";
                }
                return "{$name}{$year_suffix}{$archive_title_suffix}";
            }
        }
        // @codeCoverageIgnoreStart
        // Reason: Should not be reached.
        return "Termine{$archive_title_suffix}";
        // @codeCoverageIgnoreEnd
    }

    /** @param FullFilter $filter */
    private function getDateFilterTitleYearSuffix(array $filter): string {
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

    /** @param FullFilter $filter */
    private function getArchiveFilterTitleSuffix(array $filter): string {
        if ($filter['archiv'] === 'mit') {
            return " (Archiv)";
        }
        return "";
    }

    /** @param PartialFilter $filter */
    public function isFilterNotArchived(array $filter): bool {
        $valid_filter = $this->getValidFilter($filter);
        return $valid_filter['archiv'] === 'ohne';
    }

    public function getIsNotArchivedCriteria(): Comparison {
        $archive_threshold = $this->dateUtils()->getIsoArchiveThreshold();
        return Criteria::expr()->gte('start_date', new \DateTime($archive_threshold));
    }

    public static function fromEnv(): self {
        return new self();
    }
}
