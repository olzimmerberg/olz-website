<?php

require_once __DIR__.'/Field.php';

class StringField extends Field {
    private $max_length;
    private $allow_empty;

    public function __construct(string $id, $config = []) {
        parent::__construct($id, $config);
        $this->max_length = $config['max_length'] ?? null;
        $this->allow_empty = $config['allow_empty'] ?? false;
    }

    public function getMaxLength() {
        return $this->max_length;
    }

    public function getAllowEmpty() {
        return $this->allow_empty;
    }

    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_string($value)) {
                $validation_errors[] = "Wert muss eine Zeichenkette sein.";
            }
        }
        if (!$this->allow_empty) {
            if ($value === '') {
                if ($this->getDefaultValue() === null) {
                    $validation_errors[] = "Feld darf nicht leer sein.";
                }
            }
        }
        if ($this->max_length !== null) {
            if (strlen($value) > $this->max_length) {
                $validation_errors[] = "Wert darf maximal {$this->max_length} Zeichen lang sein.";
            }
        }
        return $validation_errors;
    }

    public function parse($string) {
        return $string;
    }

    public function getTypeScriptType() {
        return $this->getAllowNull() ? 'string|null' : 'string';
    }
}
