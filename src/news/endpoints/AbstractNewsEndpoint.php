<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';

abstract class AbstractNewsEndpoint extends OlzEndpoint {
    public static function getNewsDataField() {
        return new FieldTypes\ObjectField([
            'export_as' => 'OlzNewsData',
            'field_structure' => [
                'ownerUserId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'ownerRoleId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
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
                'onOff' => new FieldTypes\BooleanField(['default_value' => true]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'fileIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
        ]);
    }

    protected function getTagsForDb($tags) {
        return ' '.implode(' ', $tags ?? []).' ';
    }
}
