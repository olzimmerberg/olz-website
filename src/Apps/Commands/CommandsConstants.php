<?php

namespace Olz\Apps\Commands;

use PhpTypeScriptApi\Fields\FieldTypes;

class CommandsConstants {
    public const VALID_INFO_TYPES = ['email', 'firstName', 'lastName', 'gender', 'street', 'postalCode', 'city', 'region', 'countryCode', 'birthdate', 'phone', 'siCardNumber', 'solvNumber', 'string', 'enum', 'reservation'];

    public static function getRegistrationInfoField() {
        return new FieldTypes\ObjectField([
            'export_as' => 'OlzRegistrationInfo',
            'field_structure' => [
                'type' => new FieldTypes\EnumField([
                    'allowed_values' => CommandsConstants::VALID_INFO_TYPES,
                ]),
                'isOptional' => new FieldTypes\BooleanField(),
                'title' => new FieldTypes\StringField(['allow_empty' => false]),
                'description' => new FieldTypes\StringField(['allow_empty' => true]),
                'options' => new FieldTypes\ArrayField([
                    'item_field' => self::getOptionField(),
                    'allow_null' => true,
                ]),
            ],
        ]);
    }

    public static function getOptionField() {
        return new FieldTypes\ChoiceField([
            'field_map' => [
                'text' => new FieldTypes\StringField(),
            ],
        ]);
    }
}