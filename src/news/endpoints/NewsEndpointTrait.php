<?php

use PhpTypeScriptApi\Fields\FieldTypes;

trait NewsEndpointTrait {
    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzNewsDataOrNull' : 'OlzNewsData',
            'field_structure' => [
                'author' => new FieldTypes\StringField(['allow_null' => true]),
                'authorUserId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'authorRoleId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'title' => new FieldTypes\StringField([]),
                'teaser' => new FieldTypes\StringField(['allow_empty' => true]),
                'content' => new FieldTypes\StringField(['allow_empty' => true]),
                'externalUrl' => new FieldTypes\StringField(['allow_null' => true]),
                'tags' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'terminId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
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

    protected function getTagsForDb($tags) {
        return ' '.implode(' ', $tags ?? []).' ';
    }
}
