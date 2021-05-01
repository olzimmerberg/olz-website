<?php

require_once __DIR__.'/ValidationError.php';

class FieldUtils {
    public function validate($fields, $input, $options = []) {
        $validated = [];
        $errors = [];
        foreach ($fields as $field) {
            $field_id = $field->getId();
            $value = $input[$field_id] ?? null;
            if ($options['parse'] ?? false) {
                $value = $field->parse($value);
            }
            $validation_errors = $field->getValidationErrors($value);
            if (empty($validation_errors)) {
                $validated[$field_id] = $value;
            } else {
                $errors[$field_id] = $validation_errors;
            }
        }
        foreach ($input as $field_id => $value) {
            if (!array_key_exists($field_id, $validated) && !array_key_exists($field_id, $errors)) {
                $errors[$field_id] = ["Feld existiert nicht: {$field_id}"];
            }
        }
        if (!empty($errors)) {
            throw new ValidationError($errors);
        }
        return $validated;
    }

    public static function fromEnv() {
        return new self();
    }
}
