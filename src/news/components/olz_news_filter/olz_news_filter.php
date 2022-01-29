<?php

function olz_news_filter($args = []): string {
    global $_GET;

    require_once __DIR__.'/../../../utils/NewsUtils.php';

    $news_utils = NewsUtils::fromEnv();
    $current_filter = json_decode($_GET['filter'] ?? '{}', true);
    $out = "";
    $out .= "<div style='padding:4px 3px 10px 3px;'>";

    // echo "<b>News-Typ: </b>";
    // $type_options = $news_utils->getUiTypeFilterOptions($current_filter);
    // echo implode(" | ", array_map(function ($option) {
    //     $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
    //     $enc_json_filter = urlencode(json_encode($option['new_filter']));
    //     $name = $option['name'];
    //     $ident = $option['ident'];
    //     return "<a href='?filter={$enc_json_filter}' id='filter-type-{$ident}'{$selected}>{$name}</a>";
    // }, $type_options));

    $out .= "<b>Datum: </b>";
    $date_range_options = $news_utils->getUiDateRangeFilterOptions($current_filter);
    $out .= implode(" | ", array_map(function ($option) {
        $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
        $enc_json_filter = urlencode(json_encode($option['new_filter']));
        $name = $option['name'];
        $ident = $option['ident'];
        return "<a href='?filter={$enc_json_filter}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
    }, $date_range_options));

    $out .= "<br /><b>Archiv: </b>";
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
