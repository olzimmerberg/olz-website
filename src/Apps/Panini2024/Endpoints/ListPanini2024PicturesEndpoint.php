<?php

namespace Olz\Apps\Panini2024\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class ListPanini2024PicturesEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'ListPanini2024PicturesEndpoint';
    }

    public function getResponseField() {
        $panini_2024_picture_field = new FieldTypes\ObjectField([
            'export_as' => 'OlzPanini2024PictureData',
            'field_structure' => [
                'id' => new FieldTypes\IntegerField(['min_value' => 1]),
                'line1' => new FieldTypes\StringField(['allow_null' => false]),
                'line2' => new FieldTypes\StringField(['allow_null' => true]),
                'association' => new FieldTypes\StringField(['allow_null' => true]),
                'imgSrc' => new FieldTypes\StringField(['allow_null' => false]),
                'imgStyle' => new FieldTypes\StringField(['allow_null' => false]),
                'isLandscape' => new FieldTypes\BooleanField(['allow_null' => false]),
                'hasTop' => new FieldTypes\BooleanField(['allow_null' => false]),
            ],
        ]);
        return new FieldTypes\ArrayField([
            'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                'data' => $panini_2024_picture_field,
            ]]),
        ]);
    }

    public function getRequestField() {
        $skill_filter = new FieldTypes\ChoiceField([
            'field_map' => [
                'idIs' => new FieldTypes\IntegerField(['min_value' => 1]),
                'page' => new FieldTypes\IntegerField(['min_value' => 1]),
            ],
            'allow_null' => true,
        ]);
        return new FieldTypes\ObjectField(['field_structure' => [
            'filter' => $skill_filter,
        ]]);
    }

    protected function handle($input) {
        $this->checkPermission('panini2024');

        // TODO: Implement

        return [];
    }
}
