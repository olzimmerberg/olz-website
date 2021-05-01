<?php

require_once __DIR__.'/Field.php';

class BooleanField extends Field {
    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_bool($value)) {
                $validation_errors[] = "Wert muss Ja oder Nein sein.";
            }
        }
        return $validation_errors;
    }

    public function parse($string) {
        switch ($string) {
            case 'true':
            case '1':
                return true;
            case 'false':
            case '0':
                return false;
            case '':
                return null;
            default:
                throw new Exception("Unlesbarer Binärwert: '{$string}'");
        }
    }

    public function getTypeScriptType() {
        return $this->getAllowNull() ? 'boolean|null' : 'boolean';
    }
}
