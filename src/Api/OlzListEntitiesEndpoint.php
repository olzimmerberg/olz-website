<?php

namespace Olz\Api;

use Olz\Entity\Common\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzListEntitiesEndpoint extends OlzEntityEndpoint {
    public function getResponseField(): FieldTypes\Field {
        $custom_field = $this->getCustomResponseField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        $custom_item_field = $this->getCustomItemResponseField();
        $custom_item_fields = $custom_item_field ? ['custom' => $custom_item_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'items' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                    ...$custom_item_fields,
                    'id' => $this->getIdField(/* allow_null= */ false),
                    'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
                    'data' => $this->getEntityDataField(/* allow_null= */ false),
                ]]),
            ]),
        ]]);
    }

    protected function getCustomResponseField() {
        return null;
    }

    protected function getCustomItemResponseField() {
        return null;
    }

    public function getRequestField(): FieldTypes\Field {
        $custom_field = $this->getCustomRequestField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
        ]]);
    }

    protected function getCustomRequestField() {
        return null;
    }
}
