<?php

namespace Olz\Termine\Components\OlzTermineFilter;

use Olz\Components\Common\OlzComponent;
use Olz\Termine\Utils\TermineUtils;

/**
 * @phpstan-import-type FullFilter from TermineUtils
 *
 * @extends OlzComponent<array{currentFilter: FullFilter}>
 */
class OlzTermineFilter extends OlzComponent {
    public function getHtml(mixed $args): string {
        $termine_utils = $this->termineUtils()->loadTypeOptions();
        $code_href = $this->envUtils()->getCodeHref();
        $out = "";
        $out .= "<div class='olz-termine-filter'>";

        $type_options = $termine_utils->getUiTypeFilterOptions($args['currentFilter']);
        $type_options_out = implode(' ', array_map(function ($option) use ($termine_utils, $code_href) {
            $selected = $option['selected'] ? " selected" : "";
            $serialized_filter = $termine_utils->serialize($option['new_filter']);
            $name = $option['name'];
            $icon = $option['icon'];
            $icon_html = $icon ? "<img src='{$icon}' alt='' class='type-filter-icon'>" : '';
            $ident = $option['ident'];
            return <<<ZZZZZZZZZZ
                <a
                    href='{$code_href}termine?filter={$serialized_filter}'
                    class='filter type{$selected}'
                    id='filter-type-{$ident}'
                >
                    {$icon_html}{$name}
                </a>
                ZZZZZZZZZZ;
        }, $type_options));
        $out .= "<div class='filters'><div class='title'>Termin-Typ: </div>{$type_options_out}</div>";

        $date_range_options = $termine_utils->getUiDateRangeFilterOptions($args['currentFilter']);
        $date_range_options_out = implode(' ', array_map(function ($option) use ($termine_utils, $code_href) {
            $selected = $option['selected'] ? " selected" : "";
            $serialized_filter = $termine_utils->serialize($option['new_filter']);
            $name = $option['name'];
            $ident = $option['ident'];
            return <<<ZZZZZZZZZZ
                <a
                    href='{$code_href}termine?filter={$serialized_filter}'
                    class='filter date{$selected}'
                    id='filter-date-{$ident}'
                >
                    {$name}
                </a>
                ZZZZZZZZZZ;
        }, $date_range_options));
        $archive_out = $termine_utils->hasArchiveAccess() ? '' : " | <a href='#login-dialog'>ältere</a>";
        $out .= "<div class='filters'><div class='title'>Datum: </div>{$date_range_options_out}{$archive_out}</div>";

        $out .= "</div>";
        return $out;
    }
}
