<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../model/SolvResult.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$solv_results_table = new DbTable('SolvResult', 'solv_results', [
    new DbInteger('event', 'event', ['primary_key' => true]),
    new DbString('class', 'class', ['primary_key' => true]),
    new DbInteger('person', 'person', []),
    new DbString('name', 'name', ['primary_key' => true]),
    new DbString('birth_year', 'birth_year', ['primary_key' => true]),
    new DbString('domicile', 'domicile', ['primary_key' => true]),
    new DbString('club', 'club', []),
    new DbInteger('rank', 'rank', []),
    new DbInteger('result', 'result', []),
    new DbString('splits', 'splits', []),
    new DbInteger('finish_split', 'finish_split', []),
    new DbInteger('class_distance', 'class_distance', []),
    new DbInteger('class_elevation', 'class_elevation', []),
    new DbInteger('class_control_count', 'class_control_count', []),
    new DbInteger('class_competitor_count', 'class_competitor_count', []),
]);

function solv_result_from_row($row) {
    global $solv_results_table;
    return get_obj_from_assoc($solv_results_table, $row);
}

function get_unassigned_solv_results() {
    global $db, $solv_results_table;
    $sql = "SELECT * FROM `{$solv_results_table->db_name}` WHERE `person`='0'";
    return $db->query($sql);
}

function get_all_assigned_solv_result_person_data() {
    global $db, $solv_results_table;
    $sql = "
        SELECT DISTINCT
            `person`,
            `name`,
            `birth_year`,
            `domicile`
        FROM `{$solv_results_table->db_name}`
        WHERE `person`!='0'
    ";
    return $db->query($sql);
}

function get_exact_person_id($solv_result) {
    global $db, $solv_results_table;
    $sane_name = DBEsc($solv_result->name);
    $sane_birth_year = DBEsc($solv_result->{$birth_year});
    $sane_domicile = DBEsc($solv_result->{$domicile});
    $sql = "
        SELECT
            person
        FROM `{$solv_results_table->db_name}`
        WHERE
            `name`='{$sane_name}'
            AND `birth_year`='{$sane_birth_year}'
            AND `domicile`='{$sane_domicile}'
            AND `person`!='0'
    ";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    if (!$row) {
        return 0;
    }
    return intval($row['person']);
}

function solv_results_merge_person($id, $new_id) {
    global $db, $solv_results_table;
    $sane_id = intval($id);
    $sane_new_id = intval($new_id);
    $sql = "UPDATE `{$solv_results_table->db_name}` SET `person`='{$sane_new_id}' WHERE `person`='{$sane_id}'";
    return get_update_result($db->query($sql), $db);
}

function solv_person_has_results_assigned($id) {
    global $db, $solv_results_table;
    $sane_id = intval($id);
    $sql = "SELECT id FROM `{$solv_results_table->db_name}` WHERE `person`='{$sane_id}'";
    $result = $db->query($sql);
    return $result->num_rows > 0;
}

function insert_solv_result($solv_result) {
    global $db, $solv_results_table;
    $sql = get_insert_sql($solv_results_table, $solv_result);
    return get_insert_result($db->query($sql), $db);
}

function update_solv_result($solv_result) {
    global $db, $solv_results_table;
    $sql = get_update_sql($solv_results_table, $solv_result);
    return get_update_result($db->query($sql), $db);
}

function delete_solv_result_by_uid($solv_uid) {
    global $db, $solv_results_table;
    $sql = get_delete_sql_from_primary_key($solv_results_table, $solv_uid);
    return get_delete_result($db->query($sql), $db);
}
