<?php

require_once __DIR__.'/api.php';
require_once __DIR__.'/ValidationError.php';

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
