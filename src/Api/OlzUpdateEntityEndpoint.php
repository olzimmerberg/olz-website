<?php

namespace Olz\Api;

use Olz\Entity\Common\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzUpdateEntityEndpoint extends OlzEntityEndpoint {
    public function getResponseField() {
        $custom_field = $this->getCustomResponseField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'status' => $this->getStatusField(),
            'id' => $this->getIdField(/* allow_null= */ false),
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
            // It is possible to update just `meta` or just `data`, for example.
            'meta' => OlzEntity::getMetaField(/* allow_null= */ true),
            'data' => $this->getEntityDataField(/* allow_null= */ true),
        ]]);
    }

    protected function getCustomRequestField() {
        return null;
    }
}
