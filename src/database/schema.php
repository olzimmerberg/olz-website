<?php

require_once __DIR__.'/../config/database.php';

class DbTable {
    public $db_name;
    public $obj_name;
    public $fields;

    public function __construct($obj_name, $db_name, $fields) {
        $this->obj_name = $obj_name;
        $this->db_name = $db_name;
        $this->fields = $fields;
    }

    public function get_mysql_schema() {
        $sql = "CREATE TABLE `{$this->db_name}` (\n";
        $primary_key_names = [];
        $field_sqls = [];
        foreach ($this->fields as $field) {
            $field_sqls[] = $field->get_mysql_schema_line();
        }
        $fields_sql = implode(",\n  ", $field_sqls);
        $sql .= "  {$fields_sql}\n";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";
        return $sql;
    }

    //
    // public function parse_mysql_schema($sql) {
    //     $has_match = preg_match("/CREATE TABLE `?{$this->db_name}`? \\((.*)/is", $sql, $matches);
    //     if (!$has_match) {
    //         return null;
    //     }
    //     $rest_sql = $matches[1];
    //     $field_regex = ("/^"
    //         ."\\s*`?([a-z0-9_]+)`?" // field name
    //         ."\\s*([^\\s]+)" // field type
    //         ."\\s*(NULL|NOT NULL)?" // null
    //         ."([^,]+),"
    //         ."(.*)/is");
    //     while (true) {
    //         $field_match = preg_match($field_regex, $rest_sql, $matches);
    //         echo $matches[1]."<br>\n";
    //         echo $matches[2]."<br>\n";
    //         echo $matches[3]."<br>\n";
    //         echo $matches[4]."<br>\n";
    //         echo "<br>\n";
    //         if (!$field_match) {
    //             break;
    //         }
    //         $rest_sql = $matches[5];
    //     }
    //     echo 'PARSE SQL';
    //     echo nl2br("\n");
    // }
}

class DbField {
    public $db_name;
    public $obj_name;

    public function __construct($obj_name, $db_name, $specific_config = []) {
        $this->obj_name = $obj_name;
        $this->db_name = $db_name;
        $default_config = [
            'nullable' => false,
            'primary_key' => false,
            'auto_increment' => false,
            'default' => null,
        ];
        $config = array_merge($default_config, $specific_config);
        $this->nullable = $config['nullable'];
        $this->primary_key = $config['primary_key'];
        $this->auto_increment = $config['auto_increment'];
        $this->default = $config['default'];
    }

    public function db_null() {
        return $this->nullable ? 'NULL' : 'NOT NULL';
    }

    public function value_for_db($value) {
        if ($value === null) {
            return 'NULL';
        }
        $str = DBEsc($value);
        return "'{$str}'";
    }

    public function value_for_obj($value) {
        return $value;
    }

    public function get_mysql_schema_line() {
        $sql = "";
        $sql .= "`{$this->db_name}` ";
        $sql .= "{$this->db_type()} ";
        $sql .= "{$this->db_null()}";
        if ($this->default !== null) {
            $sql .= " DEFAULT {$this->default}";
        }
        if ($this->auto_increment) {
            $sql .= " AUTO_INCREMENT";
        }
        if ($this->primary_key) {
            $sql .= " PRIMARY KEY";
        }
        return $sql;
    }
}

class DbBoolean extends DbField {
    public function db_type() {
        return "int";
    }

    public function value_for_db($value) {
        if ($this->nullable && $value === null) {
            return 'NULL';
        }
        $str = DBEsc(intval($value));
        return "'{$str}'";
    }
}

class DbInteger extends DbField {
    public function db_type() {
        return "int";
    }

    public function value_for_db($value) {
        if ($this->nullable && $value === null) {
            return 'NULL';
        }
        $str = DBEsc(intval($value));
        return "'{$str}'";
    }
}

class DbString extends DbField {
    public function db_type() {
        return "text";
    }

    public function value_for_db($value) {
        if ($this->nullable && $value === null) {
            return 'NULL';
        }
        $str = DBEsc($value);
        return "'{$str}'";
    }
}

class DbEnum extends DbField {
    public function __construct($obj_name, $db_name, $options) {
        $this->obj_name = $obj_name;
        $this->db_name = $db_name;
        $this->options = $options;
    }

    public function db_type() {
        return "text";
    }

    public function value_for_db($value) {
        if ($this->nullable && $value === null) {
            return 'NULL';
        }
        $str = DBEsc($value);
        return "'{$str}'";
    }
}

class DbDate extends DbField {
    public function db_type() {
        return "date";
    }

    public function value_for_db($value) {
        $res = preg_match('/[0-9]+\-[0-9]{2}\-[0-9]{2}/', $value);
        if (!$res) {
            return 'NULL';
        }
        $str = DBEsc($value);
        return "'{$str}'";
    }
}

class DbTimestamp extends DbField {
    public function db_type() {
        return "timestamp";
    }

    public function value_for_db($value) {
        $res = preg_match('/[0-9]+\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $value);
        if (!$res) {
            return 'NULL';
        }
        $str = DBEsc($value);
        return "'{$str}'";
    }
}
