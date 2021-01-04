<?php

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
