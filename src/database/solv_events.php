<?php

require_once __DIR__.'/../admin/olz_init.php';
require_once __DIR__.'/common.php';
require_once __DIR__.'/schema.php';

$solv_events_table = new DbTable('SolvEvents', 'solv_events', [
    new DbString('club', 'club', []),
    new DbInteger('coord_x', 'coord_x', []),
    new DbInteger('coord_y', 'coord_y', []),
    new DbDate('date', 'date', []),
    new DbEnum('day_night', 'day_night', []),
    new DbDate('deadline', 'deadline', []),
    new DbInteger('duration', 'duration', []),
    new DbInteger('entryportal', 'entryportal', []),
    new DbString('kind', 'kind', []),
    new DbTimestamp('last_modification', 'last_modification', []),
    new DbString('link', 'link', []),
    new DbString('location', 'location', []),
    new DbString('map', 'map', []),
    new DbString('name', 'name', []),
    new DbInteger('national', 'national', []),
    new DbString('region', 'region', []),
    new DbInteger('solv_uid', 'solv_uid', ['primary_key' => true]),
    new DbString('type', 'type', []),
]);

// echo nl2br($solv_events_table->get_mysql_schema());
// echo nl2br($solv_events_table->parse_mysql_schema(file_get_contents(__DIR__.'/../tools/dev-data/db.sql')));

function get_solv_events_modification_index_for_year($year) {
    global $db;
    $sane_year = DBEsc($year);
    $res = $db->query("SELECT solv_uid, last_modification FROM solv_events WHERE YEAR(date)='{$sane_year}'");
    $yearly_index = [];
    for ($i = 0; $i < $res->num_rows; $i++) {
        $row = $res->fetch_assoc();
        $yearly_index[$row['solv_uid']] = $row['last_modification'];
    }
    return $yearly_index;
}

function get_solv_known_result_index_for_year($year) {
    global $db;
    $sane_year = DBEsc($year);
    $res = $db->query("SELECT solv_uid, rank_link FROM solv_events WHERE YEAR(date)='{$sane_year}'");
    $yearly_index = [];
    for ($i = 0; $i < $res->num_rows; $i++) {
        $row = $res->fetch_assoc();
        $yearly_index[$row['solv_uid']] = ($row['rank_link'] !== null) ? 1 : 0;
    }
    return $yearly_index;
}

function set_result_for_solv_event($solv_uid, $rank_link) {
    global $db;
    $sane_solv_uid = DBEsc($solv_uid);
    $sane_rank_link = DBEsc($rank_link);
    $sql = "UPDATE solv_events SET rank_link='{$sane_rank_link}' WHERE solv_uid='{$sane_solv_uid}'";
    return get_update_result($db->query($sql), $db);
}

function insert_solv_event($solv_event) {
    global $db, $solv_events_table;
    $sql = get_insert_sql($solv_events_table, $solv_event);
    return get_insert_result($db->query($sql), $db);
}

function update_solv_event($solv_event) {
    global $db, $solv_events_table;
    $sql = get_update_sql($solv_events_table, $solv_event);
    return get_update_result($db->query($sql), $db);
}

function delete_solv_event_by_uid($solv_uid) {
    global $db, $solv_events_table;
    $sql = get_delete_sql_from_primary_key($solv_events_table, $solv_uid);
    return get_delete_result($db->query($sql), $db);
}
