<?php

require_once __DIR__.'/Field.php';

class DictField extends Field {
    private Field $item_field;

    public function __construct($config = []) {
        parent::__construct($config);
        $item_field = $config['item_field'] ?? [];
        if (!($item_field instanceof Field)) {
            throw new Exception("`item_field` must be an instance of `Field`");
        }
        $this->item_field = $item_field;
    }

    public function getItemField() {
        return $this->item_field;
    }

    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_array($value)) {
                $validation_errors[] = "Wert muss ein Objekt sein.";
                return $validation_errors;
            }
            foreach ($value as $key => $item_value) {
                $item_errors = $this->item_field->getValidationErrors($item_value);
                foreach ($item_errors as $item_error) {
                    $validation_errors[] = "Schlüssel '{$key}': {$item_error}";
                }
            }
        }
        return $validation_errors;
    }

    public function parse($string) {
        throw new Exception("Unlesbares Feld: DictField");
    }

    public function getTypeScriptType($config = []) {
        $should_substitute = $config['should_substitute'] ?? true;
        if ($this->export_as !== null && $should_substitute) {
            return $this->export_as;
        }
        $item_type = $this->item_field->getTypeScriptType();
        $or_null = $this->getAllowNull() ? '|null' : '';
        return "{[key: string]: {$item_type}}{$or_null}";
    }

    public function getExportedTypeScriptTypes() {
        return array_merge(
            parent::getExportedTypeScriptTypes(),
            $this->item_field->getExportedTypeScriptTypes(),
        );
    }
}
