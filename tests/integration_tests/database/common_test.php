<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../document-root/');

require_once __DIR__.'/../../../src/database/common.php';
require_once __DIR__.'/../../../src/database/schema.php';

class ObjectWithZeroFields {
}

$zero_fields_table = new DbTable('ObjectWithZeroFields', 'zero_fielders', [
]);

class ObjectWithOneField {
    private $one_and_only;

    private $valid_field_names = [
        'one_and_only' => true,
    ];

    public function getFieldValue($field_name) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue($field_name, $new_field_value) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_field_value;
    }
}

$one_field_table = new DbTable('ObjectWithOneField', 'one_fielders', [
    new DbString('one_and_only', 'one_and_only', ['primary_key' => true]),
]);

class ObjectWithMultipleFields {
    private $first;
    private $second;
    private $third;

    private $valid_field_names = [
        'first' => true,
        'second' => true,
        'third' => true,
    ];

    public function getFieldValue($field_name) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue($field_name, $new_field_value) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_field_value;
    }
}

$multiple_fields_table = new DbTable('ObjectWithMultipleFields', 'multiple_fielders', [
    new DbString('first', 'first', ['primary_key' => true]),
    new DbInteger('second', 'second', []),
    new DbDate('third', 'third', []),
]);

/**
 * @internal
 * @coversNothing
 */
final class DatabaseCommonTest extends TestCase {
    public function testZeroFieldsTableInsert(): void {
        global $zero_fields_table;

        $sql = get_insert_sql($zero_fields_table, new ObjectWithZeroFields());

        $this->assertSame("INSERT INTO `zero_fielders` () VALUES ()", $sql);
    }

    public function testOneFieldTableInsert(): void {
        global $one_field_table;

        $sql = get_insert_sql($one_field_table, new ObjectWithOneField());

        $this->assertSame("INSERT INTO `one_fielders` (`one_and_only`) VALUES ('')", $sql);
    }

    public function testMultipleFieldsTableInsert(): void {
        global $multiple_fields_table;

        $sql = get_insert_sql($multiple_fields_table, new ObjectWithMultipleFields());

        $this->assertSame("INSERT INTO `multiple_fielders` (`first`, `second`, `third`) VALUES ('', '0', NULL)", $sql);
    }

    public function testZeroFieldsTableUpdate(): void {
        global $zero_fields_table;

        $sql = get_update_sql($zero_fields_table, new ObjectWithZeroFields());

        $this->assertSame(";", $sql);
    }

    public function testOneFieldTableUpdate(): void {
        global $one_field_table;

        $sql = get_update_sql($one_field_table, new ObjectWithOneField());

        $this->assertSame(";", $sql);
    }

    public function testMultipleFieldsTableUpdate(): void {
        global $multiple_fields_table;

        $sql = get_update_sql($multiple_fields_table, new ObjectWithMultipleFields());

        $this->assertSame("UPDATE `multiple_fielders` SET `second`='0', `third`=NULL WHERE `first`=''", $sql);
    }

    public function testZeroFieldsTableDelete(): void {
        global $zero_fields_table;

        $sql = get_delete_sql_from_primary_key($zero_fields_table, []);

        $this->assertSame(";", $sql);
    }

    public function testOneFieldTableDelete(): void {
        global $one_field_table;

        $sql = get_delete_sql_from_primary_key($one_field_table, '12');

        $this->assertSame("DELETE FROM `one_fielders` WHERE `one_and_only`='12'", $sql);
    }

    public function testMultipleFieldsTableDelete(): void {
        global $multiple_fields_table;

        $sql = get_delete_sql_from_primary_key($multiple_fields_table, ['first' => '123']);

        $this->assertSame("DELETE FROM `multiple_fielders` WHERE `first`='123'", $sql);
    }
}
