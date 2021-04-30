<?php

class Field {
    private string $id;
    private bool $allow_null = false;
    private $default_value;

    public function __construct(string $id, $config = []) {
        $this->id = $id;
        $this->allow_null = $config['allow_null'] ?? false;
        $this->default_value = $config['default_value'] ?? null;
    }

    public function getId() {
        return $this->id;
    }

    public function getAllowNull() {
        return $this->allow_null;
    }

    public function getDefaultValue() {
        return $this->default_value;
    }

    public function getValidationErrors($value) {
        $validation_errors = [];
        if (!$this->allow_null) {
            if ($value === null) {
                if ($this->default_value === null) {
                    $validation_errors[] = "Feld darf nicht leer sein.";
                }
            }
        }
        return $validation_errors;
    }

    public function parse($string) {
        if ($string == '') {
            return null;
        }
        return $string;
    }

    public function getTypeScriptType() {
        return 'any';
    }
}
