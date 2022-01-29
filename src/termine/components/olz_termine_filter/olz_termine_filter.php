<?php

function olz_termine_filter($args = []): string {
    global $_GET;

    require_once __DIR__.'/../../../utils/TermineUtils.php';

    $termine_utils = TermineUtils::fromEnv();
    $current_filter = json_decode($_GET['filter'] ?? '{}', true);
    $out = "";
    $out .= "<div style='padding:4px 3px 10px 3px;'>";

    $out .= "<b>Termin-Typ: </b>";
    $type_options = $termine_utils->getUiTypeFilterOptions($current_filter);
    $out .= implode(" | ", array_map(function ($option) {
        $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
        $enc_json_filter = urlencode(json_encode($option['new_filter']));
        $name = $option['name'];
        $ident = $option['ident'];
        return "<a href='termine.php?filter={$enc_json_filter}' id='filter-type-{$ident}'{$selected}>{$name}</a>";
    }, $type_options));

    $out .= "<br /><b>Datum: </b>";
    $date_range_options = $termine_utils->getUiDateRangeFilterOptions($current_filter);
    $out .= implode(" | ", array_map(function ($option) {
        $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
        $enc_json_filter = urlencode(json_encode($option['new_filter']));
        $name = $option['name'];
        $ident = $option['ident'];
        return "<a href='termine.php?filter={$enc_json_filter}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
    }, $date_range_options));

    $out .= "<br /><b>Archiv: </b>";
    $archive_options = $termine_utils->getUiArchiveFilterOptions($current_filter);
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
