<?php

namespace Olz\News\Components\OlzNewsFilter;

use Olz\Components\Common\OlzComponent;
use Olz\News\Utils\NewsFilterUtils;

class OlzNewsFilter extends OlzComponent {
    public function getHtml(array $args = []): string {
        $news_utils = NewsFilterUtils::fromEnv();
        $code_href = $this->envUtils()->getCodeHref();
        $current_filter = json_decode($this->getParams()['filter'] ?? '{}', true);
        $out = "";
        $out .= "<div class='olz-news-filter'>";

        $separator = "<span class='separator'> | </span>";
        $type_options = $news_utils->getUiFormatFilterOptions($current_filter);
        $type_options_out = implode($separator, array_map(function ($option) use ($code_href) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']));
            $name = $option['name'];
            $icon = $option['icon'];
            $icon_html = $icon ? "<img src='{$code_href}assets/icns/{$icon}' alt='' class='format-filter-icon'>" : '';
            $ident = $option['ident'];
            return "<span class='format-filter'{$selected}><a href='?filter={$enc_json_filter}' id='filter-format-{$ident}'>
                {$icon_html}{$name}
            </a></span>";
        }, $type_options));
        $out .= "<div><b>Format: </b>{$type_options_out}</div>";

        $date_range_options = $news_utils->getUiDateRangeFilterOptions($current_filter);
        $date_range_options_out = implode(" | ", array_map(function ($option) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']));
            $name = $option['name'];
            $ident = $option['ident'];
            return "<a href='?filter={$enc_json_filter}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
        }, $date_range_options));
        $out .= "<div><b>Datum: </b>{$date_range_options_out}</div>";

        $archive_options = $news_utils->getUiArchiveFilterOptions($current_filter);
        $archive_options_out = implode(" | ", array_map(function ($option) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']));
            $name = $option['name'];
            $ident = $option['ident'];
            return "<a href='?filter={$enc_json_filter}' id='filter-archive-{$ident}'{$selected}>{$name}</a>";
        }, $archive_options));
        $out .= "<div><b>Archiv: </b>{$archive_options_out}</div>";

        $out .= "</div>";
        return $out;
    }
}
