<?php

require_once __DIR__.'/Field.php';

class EnumField extends Field {
    private $allowed_value_map = [];

    public function __construct(string $id, $config = []) {
        parent::__construct($id, $config);
        $allowed_values = $config['allowed_values'] ?? [];
        $this->allowed_value_map = [];
        foreach ($allowed_values as $allowed_value) {
            $this->allowed_value_map[$allowed_value] = true;
        }
    }

    public function getAllowedValues() {
        return array_keys($this->allowed_value_map);
    }

    public function getValidationErrors($value) {
        $validation_errors = parent::getValidationErrors($value);
        if ($value !== null) { // The null case has been handled by the parent.
            if (!is_scalar($value)) {
                $validation_errors[] = "Feld hat ungültigen Wert.";
            } else {
                $is_allowed_value = $this->allowed_value_map[$value] ?? false;
                if (!$is_allowed_value) {
                    $validation_errors[] = "Feld hat ungültigen Wert.";
                }
            }
        }
        return $validation_errors;
    }
}
