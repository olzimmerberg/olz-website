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
}
