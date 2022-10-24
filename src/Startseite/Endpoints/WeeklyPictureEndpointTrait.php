<?php

namespace Olz\Startseite\Endpoints;

use PhpTypeScriptApi\Fields\FieldTypes;

trait WeeklyPictureEndpointTrait {
    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzWeeklyPictureDataOrNull' : 'OlzWeeklyPictureData',
            'field_structure' => [
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'imageId' => new FieldTypes\StringField([]),
                'alternativeImageId' => new FieldTypes\StringField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }
}
