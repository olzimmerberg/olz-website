<?php

namespace Olz\News\Components\OlzNewsFilter;

use Olz\Components\Common\OlzComponent;
use Olz\News\Utils\NewsUtils;

/**
 * @phpstan-import-type FullFilter from NewsUtils
 *
 * @extends OlzComponent<array{currentFilter: FullFilter}>
 */
class OlzNewsFilter extends OlzComponent {
    public function getHtml(mixed $args): string {
        $news_utils = $this->newsUtils();
        $code_href = $this->envUtils()->getCodeHref();
        $out = "";
        $out .= "<div class='olz-news-filter'>";

        $separator = "<span class='separator'> | </span>";
        $type_options = $news_utils->getUiFormatFilterOptions($args['currentFilter']);
        $type_options_out = implode($separator, array_map(function ($option) use ($news_utils, $code_href) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $serialized_filter = $news_utils->serialize($option['new_filter']);
            $url = "?filter={$serialized_filter}&seite=1";
            $name = $option['name'];
            $icon = $option['icon'] ?? null;
            $icon_html = $icon ? "<img src='{$code_href}assets/icns/{$icon}' alt='' class='format-filter-icon'>" : '';
            $ident = $option['ident'];
            return "<span class='format-filter'{$selected}><a href='{$url}' id='filter-format-{$ident}'>
                {$icon_html}{$name}
            </a></span>";
        }, $type_options));
        $out .= "<div><b>Format: </b>{$type_options_out}</div>";

        $date_range_options = $news_utils->getUiDateRangeFilterOptions($args['currentFilter']);
        $date_range_options_out = implode(" | ", array_map(function ($option) use ($news_utils) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $serialized_filter = $news_utils->serialize($option['new_filter']);
            $url = "?filter={$serialized_filter}&seite=1";
            $name = $option['name'];
            $ident = $option['ident'];
            return "<a href='{$url}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
        }, $date_range_options));
        $archive_out = $news_utils->hasArchiveAccess() ? '' : " | <a href='#login-dialog'>ältere</a>";
        $out .= "<div><b>Datum: </b>{$date_range_options_out}{$archive_out}</div>";

        $out .= "</div>";
        return $out;
    }
}
