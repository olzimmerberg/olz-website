<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';

class CreateRegistrationEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'CreateRegistrationEndpoint';
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
            'registrationForm' => new FieldTypes\IntegerField(['min_value' => 1]),
            // see README for documentation.
            'fieldValues' => new FieldTypes\DictField(['item_field' => new FieldTypes\Field()]),
        ]]);
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
