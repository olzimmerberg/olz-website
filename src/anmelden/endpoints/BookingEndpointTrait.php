<?php

use PhpTypeScriptApi\Fields\FieldTypes;

trait BookingEndpointTrait {
    public function usesExternalId(): bool {
        return true;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return self::getBookingDataField($allow_null);
    }

    public static function getBookingDataField(bool $allow_null = false) {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzBookingDataOrNull' : 'OlzBookingData',
            'field_structure' => [
                'registrationId' => new FieldTypes\StringField(['allow_null' => false]),
                // see README for documentation.
                'values' => new FieldTypes\DictField(['item_field' => new FieldTypes\Field()]),
            ],
            'allow_null' => $allow_null,
        ]);
    }
}
