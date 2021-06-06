<?php

require_once __DIR__.'/Field.php';

class ObjectField extends Field {
    private $field_structure = [];

    public function __construct(string $id, $config = []) {
        parent::__construct($id, $config);
        $field_structure = $config['field_structure'] ?? [];
        foreach ($field_structure as $key => $field) {
            if (!($field instanceof Field)) {
                throw new Exception("`field_structure`['{$key}'] must be an instance of `Field`");
            }
        }
        $this->field_structure = $field_structure;
    }

    public function getFieldStructure() {
        return $this->field_structure;
    }

    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_array($value)) {
                $validation_errors[] = "Wert muss ein Objekt sein.";
                return $validation_errors;
            }
            foreach ($this->field_structure as $key => $field) {
                $item_value = $value[$key] ?? null;
                $item_errors = $field->getValidationErrors($item_value);
                foreach ($item_errors as $item_error) {
                    $validation_errors[] = "Schlüssel '{$key}': {$item_error}";
                }
            }
            foreach ($value as $key => $item_value) {
                if (!isset($this->field_structure[$key])) {
                    $validation_errors[] = "Überflüssiger Schlüssel '{$key}'.";
                }
            }
        }
        return $validation_errors;
    }

    public function parse($string) {
        throw new Exception("Unlesbares Feld: ObjectField");
    }

    public function getTypeScriptType() {
        $object_type = "{\n";
        foreach ($this->field_structure as $key => $field) {
            $item_type = $field->getTypeScriptType();
            $object_type .= "    '{$key}': {$item_type},\n";
        }
        $object_type .= "}";
        $or_null = $this->getAllowNull() ? '|null' : '';
        return "{$object_type}{$or_null}";
    }
}
