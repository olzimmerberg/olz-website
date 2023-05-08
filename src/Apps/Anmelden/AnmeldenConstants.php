<?php

namespace Olz\Apps\Anmelden;

use PhpTypeScriptApi\Fields\FieldTypes;

class AnmeldenConstants {
    public const VALID_INFO_TYPES = ['email', 'firstName', 'lastName', 'gender', 'street', 'postalCode', 'city', 'region', 'countryCode', 'birthdate', 'phone', 'siCardNumber', 'solvNumber', 'string', 'enum', 'reservation'];

    public static function getRegistrationInfoField() {
        return new FieldTypes\ObjectField([
            'export_as' => 'OlzRegistrationInfo',
            'field_structure' => [
                'type' => new FieldTypes\EnumField([
                    'allowed_values' => AnmeldenConstants::VALID_INFO_TYPES,
                ]),
                'isOptional' => new FieldTypes\BooleanField(),
                'title' => new FieldTypes\StringField(['allow_empty' => false]),
                'description' => new FieldTypes\StringField(['allow_empty' => true]),
                'options' => new FieldTypes\ChoiceField([
                    'field_map' => [
                        // Each option is described by some text.
                        'text' => new FieldTypes\ArrayField([
                            'item_field' => new FieldTypes\StringField(),
                        ]),
                    ],
                    'allow_null' => true,
                ]),
            ],
        ]);
    }
}
