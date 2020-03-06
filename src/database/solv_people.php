<?php

require_once __DIR__.'/../admin/olz_init.php';
require_once __DIR__.'/../model/SolvPerson.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$solv_people_table = new DbTable('SolvPerson', 'solv_people', [
    new DbInteger('id', 'id', ['primary_key' => true, 'auto_increment' => true]),
    new DbString('name', 'name', []),
    new DbString('birth_year', 'birth_year', []),
    new DbString('domicile', 'domicile', []),
    new DbInteger('member', 'member', []),
]);

function get_all_solv_people() {
    global $db, $solv_people_table;
    $sql = "SELECT * FROM `{$solv_people_table->db_name}`";
    return $db->query($sql);
}

function insert_solv_person($solv_person) {
    global $db, $solv_people_table;
    $sql = get_insert_sql($solv_people_table, $solv_person);
    return $db->query($sql) ? $db->insert_id : null;
}

function update_solv_person($solv_person) {
    global $db, $solv_people_table;
    $sql = get_update_sql($solv_people_table, $solv_person);
    return $db->query($sql);
}

function delete_solv_person_by_uid($solv_uid) {
    global $db, $solv_people_table;
    $sql = get_delete_sql_from_primary_key($solv_people_table, $solv_uid);
    return $db->query($sql);
}
