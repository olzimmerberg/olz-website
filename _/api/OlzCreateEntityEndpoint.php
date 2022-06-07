<?php

use App\Entity\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/OlzEntityEndpoint.php';

abstract class OlzCreateEntityEndpoint extends OlzEntityEndpoint {
    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => $this->getIdField(/* allow_null= */ true),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }
}
