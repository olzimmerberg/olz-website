<?php

namespace Olz\Api;

use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzAddRelationEndpoint extends OlzEndpoint {
    use \Psr\Log\LoggerAwareTrait;

    abstract public function getIdsField(): FieldTypes\ObjectField;

    public function getResponseField() {
        $custom_field = $this->getCustomResponseField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'status' => $this->getStatusField(),
        ]]);
    }

    protected function getCustomResponseField() {
        return null;
    }

    protected function getStatusField() {
        return new FieldTypes\EnumField(['allowed_values' => [
            'OK',
            'ERROR',
        ]]);
    }

    public function getRequestField() {
        $custom_field = $this->getCustomRequestField();
        $custom_fields = $custom_field ? ['custom' => $custom_field] : [];
        return new FieldTypes\ObjectField(['field_structure' => [
            ...$custom_fields,
            'ids' => $this->getIdsField(),
        ]]);
    }

    protected function getCustomRequestField() {
        return null;
    }
}