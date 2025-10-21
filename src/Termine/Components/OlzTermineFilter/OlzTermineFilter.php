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

        $separator = "<span class='separator'> | </span>";
        $type_options = $termine_utils->getUiTypeFilterOptions($args['currentFilter']);
        $type_options_out = implode($separator, array_map(function ($option) use ($code_href) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']) ?: '{}');
            $name = $option['name'];
            $icon = $option['icon'];
            $icon_html = $icon ? "<img src='{$icon}' alt='' class='type-filter-icon'>" : '';
            $ident = $option['ident'];
            return "<span class='type-filter'{$selected}><a href='{$code_href}termine?filter={$enc_json_filter}' id='filter-type-{$ident}'>
                {$icon_html}{$name}
            </a></span>";
        }, $type_options));
        $out .= "<div><b>Termin-Typ: </b>{$type_options_out}</div>";

        $date_range_options = $termine_utils->getUiDateRangeFilterOptions($args['currentFilter']);
        $date_range_options_out = implode(" | ", array_map(function ($option) use ($code_href) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']) ?: '{}');
            $name = $option['name'];
            $ident = $option['ident'];
            return "<a href='{$code_href}termine?filter={$enc_json_filter}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
        }, $date_range_options));
        $archive_out = $termine_utils->hasArchiveAccess() ? '' : " | <a href='#login-dialog'>ältere</a>";
        $out .= "<div><b>Datum: </b>{$date_range_options_out}{$archive_out}</div>";

        $out .= "</div>";
        return $out;
    }
}
