<?php

function get_obj_from_assoc($db_table, $assoc_row) {
    $class_name = $db_table->obj_name;
    $obj = new $class_name();
    foreach ($db_table->fields as $field) {
        $obj_field = $field->obj_name;
        $db_field = $field->db_name;
        $sane_value = $field->value_for_obj($assoc_row[$db_field]);
        $obj->{$obj_field} = $sane_value;
    }
    return $obj;
}

function get_insert_sql($db_table, $obj) {
    $sql_fields = [];
    $sql_values = [];
    foreach ($db_table->fields as $field) {
        $obj_field = $field->obj_name;
        $db_field = $field->db_name;
        $sane_value = $field->value_for_db($obj->{$obj_field});
        if (!$field->auto_increment) {
            $sql_fields[] = "`{$db_field}`";
            $sql_values[] = $sane_value;
        }
    }
    $sql_fields_str = implode(', ', $sql_fields);
    $sql_values_str = implode(', ', $sql_values);
    return "INSERT INTO `{$db_table->db_name}` ({$sql_fields_str}) VALUES ({$sql_values_str})";
}

function get_insert_result($result, $db) {
    $has_error = !$result || $db->errno !== 0;
    if ($has_error) {
        return [
            'has_error' => true,
            'error' => $db->error,
            'code' => $db->errno,
        ];
    }
    return [
        'has_error' => false,
        'insert_id' => $db->insert_id,
        'affected_rows' => $db->affected_rows,
    ];
}

function get_update_sql($db_table, $obj) {
    $sql_assignments = [];
    $sql_constraints = [];
    foreach ($db_table->fields as $field) {
        $obj_field = $field->obj_name;
        $db_field = $field->db_name;
        $sane_value = $field->value_for_db($obj->{$obj_field});
        if ($field->primary_key) {
            $sql_constraints[] = "`{$db_field}`={$sane_value}";
        } else {
            $sql_assignments[] = "`{$db_field}`={$sane_value}";
        }
    }
    if (count($sql_assignments) == 0 || count($sql_constraints) == 0) {
        return ";";
    }
    $sql_assignments_str = implode(', ', $sql_assignments);
    $sql_constraints_str = implode(' AND ', $sql_constraints);
    return "UPDATE `{$db_table->db_name}` SET {$sql_assignments_str} WHERE {$sql_constraints_str}";
}

function get_update_result($result, $db) {
    $has_error = !$result || $db->errno !== 0;
    if ($has_error) {
        return [
            'has_error' => true,
            'error' => $db->error,
            'code' => $db->errno,
        ];
    }
    return [
        'has_error' => false,
        'affected_rows' => $db->affected_rows,
    ];
}

function get_delete_sql_from_primary_key($db_table, $primary_key) {
    $sql_constraints = [];
    foreach ($db_table->fields as $field) {
        if ($field->primary_key) {
            $obj_field = $field->obj_name;
            $db_field = $field->db_name;
            $value = is_array($primary_key) ? $primary_key[$obj_field] : $primary_key;
            $sane_value = $field->value_for_db($value);
            $sql_constraints[] = "`{$db_field}`={$sane_value}";
        }
    }
    if (count($sql_constraints) == 0) {
        return ";";
    }
    $sql_constraints_str = implode(' AND ', $sql_constraints);
    return "DELETE FROM `{$db_table->db_name}` WHERE {$sql_constraints_str}";
}

function get_delete_result($result, $db) {
    $has_error = !$result || $db->errno !== 0;
    if ($has_error) {
        return [
            'has_error' => true,
            'error' => $db->error,
            'code' => $db->errno,
        ];
    }
    return [
        'has_error' => false,
        'affected_rows' => $db->affected_rows,
    ];
}
