<?php

require_once __DIR__.'/../model/SolvEvent.php';

$field_by_csv_column = [
    "unique_id" => "solv_uid",
    "date" => "date",
    "duration" => "duration",
    "kind" => "kind",
    "day_night" => "day_night",
    "national" => "national",
    "region" => "region",
    "type" => "type",
    "event_name" => "name",
    "event_link" => "link",
    "club" => "club",
    "map" => "map",
    "location" => "location",
    "coord_x" => "coord_x",
    "coord_y" => "coord_y",
    "deadline" => "deadline",
    "entryportal" => "entryportal",
    "last_modification" => "last_modification",
];
$solv_entryportals = [
    1 => "GO2OL",
    2 => "picoTIMING",
    3 => "anderes",
];

function parse_solv_events_csv($csv_content) {
    global $field_by_csv_column;

    $data = str_getcsv($csv_content, "\n");
    $header = str_getcsv($data[0], ";");
    $solv_events = [];
    for ($row_index = 1; $row_index < count($data); $row_index++) {
        $row = str_getcsv($data[$row_index], ";");
        $solv_event = new SolvEvent();
        for ($col_index = 0; $col_index < count($header); $col_index++) {
            $csv_column_name = $header[$col_index];
            $solv_event_field = $field_by_csv_column[$csv_column_name];
            $field_value = $row[$col_index];
            $solv_event->setFieldValue($solv_event_field, $field_value);
        }
        $solv_events[] = $solv_event;
    }
    return $solv_events;
}
