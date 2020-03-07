<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/../database/solv_events.php';
require_once __DIR__.'/../database/solv_people.php';
require_once __DIR__.'/../database/solv_results.php';
require_once __DIR__.'/../fetchers/solv_events.php';
require_once __DIR__.'/../fetchers/solv_results.php';
require_once __DIR__.'/../parsers/solv_events.php';
require_once __DIR__.'/../parsers/solv_results.php';

$solv_maintainer_email = 'simon.hatt@olzimmerberg.ch';

class SyncSolvTask extends BackgroundTask {
    protected static function get_ident() {
        return "SyncSolv";
    }

    protected function run_specific_task() {
        $this->sync_solv_events();
        $this->sync_solv_results();
        $this->sync_solv_people();
        $this->merge_solv_people();
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
                    if ($res['has_error']) {
                        $res_str = json_encode($res);
                        $result_str = json_encode($result);
                        $this->log_warning("Result could not be inserted: {$res_str} {$result_str}");
                    }
                }
                set_result_for_solv_event($solv_uid, $event_result['result_list_id']);
            }
        }
    }

    private function sync_solv_people() {
        $res_solv_results = get_unassigned_solv_results();
        while ($row_solv_result = $res_solv_results->fetch_assoc()) {
            $solv_result = solv_result_from_row($row_solv_result);
            $person = get_exact_person_id($solv_result);
            if ($person == 0) {
                $this->log_info("Person not exactly matched:");
                $this->log_info(json_encode($solv_result, JSON_PRETTY_PRINT));
                $person = $this->find_or_create_solv_person($solv_result);
            }
            if ($person != 0) {
                $solv_result->person = $person;
                update_solv_result($solv_result);
            }
        }
    }

    private function find_or_create_solv_person($solv_result) {
        $result = get_all_assigned_solv_result_person_data();
        $least_difference = strlen($solv_result->name);
        $rows_with_least_difference = [];
        while ($row = $result->fetch_assoc()) {
            $name_difference = levenshtein($solv_result->name, $row['name']);
            $int_birth_year = intval($solv_result->birth_year);
            $int_birth_year_row = intval($row['birth_year']);
            $birth_year_difference = levenshtein("{$int_birth_year}", "{$int_birth_year_row}");
            $trim_domicile = trim($solv_result->domicile);
            $trim_domicile_row = trim($row['domicile']);
            $domicile_difference = levenshtein($trim_domicile, $trim_domicile_row);
            if ($trim_domicile == '' || $trim_domicile_row == '') {
                $domicile_difference = min($domicile_difference, 2);
            }
            $difference = $name_difference + $birth_year_difference + $domicile_difference;
            if ($difference < $least_difference) {
                $least_difference = $difference;
                $rows_with_least_difference = [$row];
            } elseif ($difference == $least_difference) {
                $rows_with_least_difference[] = $row;
            }
        }
        if ($least_difference < 3 && count($rows_with_least_difference) == 1) {
            $this->log_info("Fuzzily matched persons (difference {$least_difference}, take first):");
            $this->log_info(json_encode($rows_with_least_difference, JSON_PRETTY_PRINT));
            return intval($rows_with_least_difference[0]['person']);
        }
        $solv_person = new SolvPerson();
        $solv_person->same_as = null;
        $solv_person->name = $solv_result->name;
        $solv_person->birth_year = $solv_result->birth_year;
        $solv_person->domicile = $solv_result->domicile;
        $solv_person->member = 1;
        $res = insert_solv_person($solv_person);
        $insert_id = $res['insert_id'];

        $person_str = json_encode($solv_person, JSON_PRETTY_PRINT);
        $closest_matches_str = json_encode($rows_with_least_difference, JSON_PRETTY_PRINT);
        $this->log_info("Created new person (id {$insert_id}):");
        $this->log_info($person_str);
        $this->log_info("Closest matches (difference {$least_difference}) were:");
        $this->log_info($closest_matches_str);
        if ($least_difference < 6 && count($rows_with_least_difference) > 0) {
            $this->log_info("Unclear case. TODO: Send mail in this case.");
        }
        return $insert_id;
    }

    private function merge_solv_people() {
        $res_merge = get_solv_people_marked_for_merge();
        while ($row = $res_merge->fetch_assoc()) {
            $id = $row['id'];
            $same_as = $row['same_as'];
            $this->log_info("Merge person {$id} into {$same_as}.");
            if (intval($same_as) <= 0) {
                $this->log_warning("Invalid same_as for person {$id}: {$same_as}.");
            } elseif (!solv_person_has_results_assigned($same_as)) {
                $this->log_warning("same_as ({$same_as}) without any results assigned for person {$id}.");
            } else {
                $merge_result = solv_results_merge_person($id, $same_as);
                if ($merge_result['has_error']) {
                    $this->log_error("Merge failed!");
                }
            }
            if (!solv_person_has_results_assigned($id)) {
                delete_solv_person_by_id($id);
            } elseif ($id == $same_as) {
                solv_person_reset_same_as($id);
            } else {
                $this->log_warning("There are still results assigned to person {$id}.");
            }
        }
    }
}
