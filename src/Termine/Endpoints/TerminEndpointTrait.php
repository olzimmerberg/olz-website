<?php

namespace Olz\Termine\Endpoints;

use PhpTypeScriptApi\Fields\FieldTypes;

trait TerminEndpointTrait {
    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminDataOrNull' : 'OlzTerminData',
            'field_structure' => [
                'startDate' => new FieldTypes\DateField(['allow_null' => false]),
                'startTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'endDate' => new FieldTypes\DateField(['allow_null' => true]),
                'endTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'title' => new FieldTypes\StringField([]),
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'link' => new FieldTypes\StringField(['allow_empty' => true]),
                'deadline' => new FieldTypes\DateTimeField(['allow_null' => true]),
                'newsletter' => new FieldTypes\BooleanField([]),
                'solvId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'go2olId' => new FieldTypes\StringField(['allow_null' => true]),
                'types' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'coordinateX' => new FieldTypes\IntegerField(['allow_null' => true]),
                'coordinateY' => new FieldTypes\IntegerField(['allow_null' => true]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'fileIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    protected function getTypesForDb($types) {
        return ' '.implode(' ', $types ?? []).' ';
    }

    protected function getTypesForApi($types) {
        $types_string = $types ?? '';
        $types_for_api = [];
        foreach (explode(' ', $types_string) as $type) {
            if (trim($type) != '') {
                $types_for_api[] = trim($type);
            }
        }
        return $types_for_api;
    }
}
