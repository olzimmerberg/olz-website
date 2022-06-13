<?php

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class GetPrefillValuesEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetPrefillValuesEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'prefillValues' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\Field(),
                'allow_null' => true,
            ]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [
                // Can be a managed user
                'userId' => new FieldTypes\IntegerField(['min_value' => 1, 'allow_null' => true]),
            ],
        ]);
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'prefillValues' => [],
        ];
    }
}
