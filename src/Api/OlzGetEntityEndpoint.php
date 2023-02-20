<?php

namespace Olz\Api;

use Olz\Entity\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzGetEntityEndpoint extends OlzEntityEndpoint {
    public function getResponseField() {
        $custom_field = $this->getCustomResponseField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'id' => $this->getIdField(/* allow_null= */ false),
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    protected function getCustomResponseField() {
        return null;
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
