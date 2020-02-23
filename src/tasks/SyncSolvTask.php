<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/../database/solv_events.php';
require_once __DIR__.'/../database/solv_results.php';
require_once __DIR__.'/../fetchers/solv_events.php';
require_once __DIR__.'/../fetchers/solv_results.php';
require_once __DIR__.'/../parsers/solv_events.php';
require_once __DIR__.'/../parsers/solv_results.php';

class SyncSolvTask extends BackgroundTask {
    protected static function get_ident() {
        return "SyncSolv";
    }

    protected function run_specific_task() {
        $this->sync_solv_events();
        $this->sync_solv_results();
    }

    private function sync_solv_events() {
        $current_year = date('Y');
        $this->sync_solv_events_for_year($current_year);
        $this->sync_solv_events_for_year($current_year - 1);
        $this->sync_solv_events_for_year($current_year + 1);
        $this->sync_solv_events_for_year($current_year - 2);
    }

    private function sync_solv_events_for_year($year) {
        $csv = fetch_solv_events_csv_for_year($year);
        $solv_events = parse_solv_events_csv($csv);
        $this->import_solv_events_for_year($solv_events, $year);
    }

    private function import_solv_events_for_year($solv_events, $year) {
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
                $this->log_info("INSERTED {$solv_event->solv_uid}");
            } elseif ($solv_event->last_modification > $modification_index[$solv_event->solv_uid]) {
                update_solv_event($solv_event);
                $this->log_info("UPDATED {$solv_event->solv_uid}");
            }
        }
        foreach ($solv_uid_still_exists as $solv_uid => $still_exists) {
            if (!$still_exists) {
                delete_solv_event_by_uid($solv_uid);
                $this->log_info("DELETED {$solv_uid}");
            }
        }
    }

    private function sync_solv_results() {
        $current_year = date('Y');
        $this->sync_solv_results_for_year($current_year);
    }

    private function sync_solv_results_for_year($year) {
        $json = fetch_solv_yearly_results_json($year);
        $result_id_by_uid = parse_solv_yearly_results_json($json);
        $known_result_index = get_solv_known_result_index_for_year($year);
        $this->import_solv_results_for_year($result_id_by_uid, $known_result_index);
    }

    private function import_solv_results_for_year($result_id_by_uid, $known_result_index) {
        global $db;
        $solv_uid_still_exists = [];
        foreach ($result_id_by_uid as $solv_uid => $event_result) {
            if (!$known_result_index[$solv_uid] && $event_result['result_list_id']) {
                $this->log_info("Event with SOLV ID {$solv_uid} has new results.");
                $html = fetch_solv_event_results_html($event_result['result_list_id']);
                $results = parse_solv_event_result_html($html, $solv_uid);
                $results_count = count($results);
                $this->log_info("Number of results fetched & parsed: {$results_count}");
                foreach ($results as $result) {
                    $res = insert_solv_result($result);
                    if (!$res) {
                        $result_str = json_encode($result);
                        $this->log_warning("Result could not be inserted: {$result_str}");
                    }
                }
                set_result_for_solv_event($solv_uid, $event_result['result_list_id']);
            }
        }
    }
}
