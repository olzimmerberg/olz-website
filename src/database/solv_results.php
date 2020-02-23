<?php

require_once __DIR__.'/../admin/olz_init.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$solv_results_table = new DbTable('SolvResult', 'solv_results', [
    new DbInteger('event', 'event', ['primary_key' => true]),
    new DbString('class', 'class', ['primary_key' => true]),
    new DbInteger('person', 'person', ['primary_key' => true]),
    new DbString('name', 'name', ['primary_key' => true]),
    new DbString('birth_year', 'birth_year', ['primary_key' => true]),
    new DbString('domicile', 'domicile', ['primary_key' => true]),
    new DbString('club', 'club', ['primary_key' => true]),
    new DbInteger('rank', 'rank', []),
    new DbInteger('result', 'result', []),
    new DbString('splits', 'splits', []),
    new DbInteger('finish_split', 'finish_split', []),
]);

function insert_solv_result($solv_result) {
    global $db, $solv_results_table;
    $sql = get_insert_sql($solv_results_table, $solv_result);
    return $db->query($sql);
}

function update_solv_result($solv_result) {
    global $db, $solv_results_table;
    $sql = get_update_sql($solv_results_table, $solv_result);
    return $db->query($sql);
}

function delete_solv_result_by_uid($solv_uid) {
    global $db, $solv_results_table;
    $sql = get_delete_sql_from_primary_key($solv_results_table, $solv_uid);
    return $db->query($sql);
}
