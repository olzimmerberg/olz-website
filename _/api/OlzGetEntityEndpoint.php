<?php

use App\Entity\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/OlzEntityEndpoint.php';

abstract class OlzGetEntityEndpoint extends OlzEntityEndpoint {
    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(),
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(),
        ]]);
    }
}
