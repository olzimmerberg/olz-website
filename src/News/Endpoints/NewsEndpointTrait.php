<?php

namespace Olz\News\Endpoints;

use PhpTypeScriptApi\Fields\FieldTypes;

trait NewsEndpointTrait {
    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzNewsDataOrNull' : 'OlzNewsData',
            'field_structure' => [
                'format' => new FieldTypes\EnumField([
                    'export_as' => 'OlzNewsFormat',
                    'allowed_values' => ['aktuell', 'galerie'],
                ]),
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
                    'allow_null' => true,
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

    protected function getTagsForApi($tags) {
        $tags_string = $tags ?? '';
        $tags_for_api = [];
        foreach (explode(' ', $tags_string) as $tag) {
            if (trim($tag) != '') {
                $tags_for_api[] = trim($tag);
            }
        }
        return $tags_for_api;
    }
}
