<?php

namespace Olz\News\Components\OlzNewsFilter;

use Olz\Components\Common\OlzComponent;
use Olz\News\Utils\NewsFilterUtils;

class OlzNewsFilter extends OlzComponent {
    public function getHtml($args = []): string {
        global $_GET;

        $news_utils = NewsFilterUtils::fromEnv();
        $data_href = $this->envUtils()->getDataHref();
        $current_filter = json_decode($_GET['filter'] ?? '{}', true);
        $out = "";
        $out .= "<div style='padding:4px 3px 10px 3px;'>";

        $out .= "<b>Format: </b>";
        $type_options = $news_utils->getUiFormatFilterOptions($current_filter);
        $out .= implode(" | ", array_map(function ($option) use ($data_href) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']));
            $name = $option['name'];
            $icon = $option['icon'];
            $icon_html = $icon ? "<img src='{$data_href}icns/{$icon}' alt='' class='format-filter-icon'>" : '';
            $ident = $option['ident'];
            return "<a href='?filter={$enc_json_filter}' id='filter-format-{$ident}' class='format-filter'{$selected}>
                {$icon_html}{$name}
            </a>";
        }, $type_options));

        $out .= "<br />";

        $out .= "<b>Datum: </b>";
        $date_range_options = $news_utils->getUiDateRangeFilterOptions($current_filter);
        $out .= implode(" | ", array_map(function ($option) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']));
            $name = $option['name'];
            $ident = $option['ident'];
            return "<a href='?filter={$enc_json_filter}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
        }, $date_range_options));

        $out .= "<br />";

        $out .= "<b>Archiv: </b>";
        $archive_options = $news_utils->getUiArchiveFilterOptions($current_filter);
        $out .= implode(" | ", array_map(function ($option) {
            $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
            $enc_json_filter = urlencode(json_encode($option['new_filter']));
            $name = $option['name'];
            $ident = $option['ident'];
            return "<a href='?filter={$enc_json_filter}' id='filter-archive-{$ident}'{$selected}>{$name}</a>";
        }, $archive_options));

        $out .= "</div>";
        return $out;
    }
}
