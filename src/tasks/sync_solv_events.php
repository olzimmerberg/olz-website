<?php

require_once __DIR__.'/../database/solv_events.php';
require_once __DIR__.'/../fetchers/solv_events.php';
require_once __DIR__.'/../parsers/solv_events.php';

function sync_solv_events() {
    $current_year = date('Y');
    sync_solv_events_for_year($current_year);
    sync_solv_events_for_year($current_year - 1);
    sync_solv_events_for_year($current_year + 1);
    sync_solv_events_for_year($current_year - 2);
}

function sync_solv_events_for_year($year) {
    $csv = fetch_solv_events_csv_for_year($year);
    $solv_events = parse_solv_events_csv($csv);
    import_solv_events_for_year($solv_events, $year);
}

function import_solv_events_for_year($solv_events, $year) {
    $modification_index = get_solv_events_modification_index_for_year($year);
    $solv_uid_still_exists = [];
    foreach ($modification_index as $solv_uid => $last_modification) {
        $solv_uid_still_exists[$solv_uid] = false;
    }
    foreach ($solv_events as $solv_event) {
        $solv_uid_still_exists[$solv_event->solv_uid] = true;
        $existed = isset($modification_index[$solv_event->solv_uid]);
        if (!$existed) {
            insert_solv_event($solv_event);
            echo "INSERTED {$solv_event->solv_uid}<br>";
        } elseif ($solv_event->last_modification > $modification_index[$solv_event->solv_uid]) {
            update_solv_event($solv_event);
            echo "UPDATED {$solv_event->solv_uid}<br>";
        }
    }
    foreach ($solv_uid_still_exists as $solv_uid => $still_exists) {
        if (!$still_exists) {
            delete_solv_event_by_uid($solv_uid);
            echo "DELETED {$solv_uid}<br>";
        }
    }
}
