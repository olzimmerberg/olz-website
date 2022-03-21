<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../constants.php';

class CreateRegistrationFormEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'CreateRegistrationFormEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'title' => new FieldTypes\StringField(['allow_empty' => false]),
            'description' => new FieldTypes\StringField(['allow_empty' => true]),
            // see README for documentation.
            'fields' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\ObjectField([
                    'field_structure' => [
                        'type' => new FieldTypes\EnumField([
                            'allowed_values' => VALID_FIELD_TYPES,
                        ]),
                        'isOptional' => new FieldTypes\BooleanField(),
                        'title' => new FieldTypes\StringField(['allow_empty' => false]),
                        'description' => new FieldTypes\StringField(['allow_empty' => true]),
                        'options' => new FieldTypes\ArrayField([
                            'item_field' => new FieldTypes\StringField(),
                            'allow_null' => true,
                        ]),
                    ],
                ]),
            ]),
            'opensAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'closesAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'ownerUser' => new FieldTypes\IntegerField(['allow_empty' => false]),
            'ownerRole' => new FieldTypes\IntegerField(['allow_empty' => true]),
        ]]);
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
