<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/OlzEntityEndpoint.php';

abstract class OlzDeleteEntityEndpoint extends OlzEntityEndpoint {
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
            'id' => $this->getIdField(),
        ]]);
    }
}
