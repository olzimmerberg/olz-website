<?php

use Olz\Entity\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/OlzEntityEndpoint.php';

abstract class OlzUpdateEntityEndpoint extends OlzEntityEndpoint {
    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => $this->getIdField(),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(),
            // It is possible to update just `meta` or just `data`, for example.
            'meta' => OlzEntity::getMetaField(/* allow_null= */ true),
            'data' => $this->getEntityDataField(/* allow_null= */ true),
        ]]);
    }
}
