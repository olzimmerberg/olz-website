<?php

require_once __DIR__.'/api.php';

class ValidationError extends Exception {
    public $validationErrors;

    public function __construct($validation_errors, Exception $previous = null) {
        parent::__construct("ValidationError", 0, $previous);
        $this->validationErrors = $validation_errors;
    }

    public function getValidationErrors() {
        return $this->validationErrors;
    }

    public function getStructuredAnswer() {
        return $this->validationErrors;
    }
}

function backend_validate($fields, $input) {
    $validated = [];
    $errors = [];
    foreach ($fields as $field) {
        $field_id = $field->getId();
        $value = $input[$field_id] ?? null;
        $validation_errors = $field->getValidationErrors($value);
        if (empty($validation_errors)) {
            $validated[$field_id] = $value;
        } else {
            $errors[$field_id] = $validation_errors;
        }
    }
    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    return $validated;
}
