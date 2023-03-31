<?php

namespace Olz\Apps\Panini2024\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Panini2024\Panini2024Picture;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

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
        $has_access = $this->authUtils()->hasPermission('panini2024');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $current_user = $this->authUtils()->getCurrentUser();
        $panini_repo = $this->entityManager()->getRepository(Panini2024Picture::class);

        return [];
    }
}
