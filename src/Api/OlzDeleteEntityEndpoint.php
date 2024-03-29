<?php

namespace Olz\Api;

use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzDeleteEntityEndpoint extends OlzEntityEndpoint {
    public function getResponseField() {
        $custom_field = $this->getCustomResponseField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'status' => $this->getStatusField(),
        ]]);
    }

    protected function getCustomResponseField() {
        return null;
    }

    protected function getStatusField() {
        return new FieldTypes\EnumField(['allowed_values' => [
            'OK',
            'ERROR',
        ]]);
    }

    public function getRequestField() {
        $custom_field = $this->getCustomRequestField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'id' => $this->getIdField(/* allow_null= */ false),
        ]]);
    }

    protected function getCustomRequestField() {
        return null;
    }
}
