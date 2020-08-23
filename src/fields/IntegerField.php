<?php

require_once __DIR__.'/NumberField.php';

class IntegerField extends NumberField {
    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_int($value)) {
                $validation_errors[] = "Wert muss eine Ganzzahl sein.";
            }
        }
        return $validation_errors;
    }
}
