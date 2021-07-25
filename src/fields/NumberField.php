<?php

require_once __DIR__.'/Field.php';

class NumberField extends Field {
    private $min_value;
    private $max_value;

    public function __construct($config = []) {
        parent::__construct($config);
        $this->min_value = $config['min_value'] ?? null;
        $this->max_value = $config['max_value'] ?? null;
    }

    public function getMinValue() {
        return $this->min_value;
    }

    public function getMaxValue() {
        return $this->max_value;
    }

    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_numeric($value)) {
                $validation_errors[] = "Wert muss eine Zahl sein.";
            }
            if ($this->min_value !== null) {
                if ($value < $this->min_value) {
                    $validation_errors[] = "Wert darf nicht kleiner als {$this->min_value} sein.";
                }
            }
            if ($this->max_value !== null) {
                if ($value > $this->max_value) {
                    $validation_errors[] = "Wert darf nicht grösser als {$this->max_value} sein.";
                }
            }
        }
        return $validation_errors;
    }

    public function parse($string) {
        if ($string == '') {
            return null;
        }
        if (preg_match('/^[0-9\\.\\-]+$/', $string)) {
            return floatval($string);
        }
        throw new Exception("Unlesbare Zahl: '{$string}'");
    }

    public function getTypeScriptType() {
        return $this->getAllowNull() ? 'number|null' : 'number';
    }
}
