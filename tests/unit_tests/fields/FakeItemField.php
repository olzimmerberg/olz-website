<?php

require_once __DIR__.'/../../../src/fields/Field.php';

class FakeItemField extends Field {
    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if ($value !== 'foo' && $value !== 'bar') {
                $validation_errors[] = "Wert muss 'foo' oder 'bar' sein.";
            }
        }
        return $validation_errors;
    }

    public function getTypeScriptType($config = []) {
        $should_substitute = $config['should_substitute'] ?? true;
        if ($this->export_as !== null && $should_substitute) {
            return $this->export_as;
        }
        return 'ItemType'.($this->getAllowNull() ? '|null' : '');
    }
}
