<?php

namespace Olz\Apps\Panini2024\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @phpstan-type OlzPanini2024PictureData array{
 *   id: int<1, max>,
 *   line1: non-empty-string,
 *   line2?: ?non-empty-string,
 *   association?: ?non-empty-string,
 *   imgSrc: non-empty-string,
 *   imgStyle: non-empty-string,
 *   isLandscape: bool,
 *   hasTop: bool,
 * }
 *
 * @extends TypedEndpoint<
 *   array{filter?: ?(
 *     array{idIs: int<1, max>}
 *     | array{page: int<1, max>}
 *   )},
 *   array<array{data: OlzPanini2024PictureData}>,
 * >
 */
class ListPanini2024PicturesEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [];
    }

    public static function getIdent(): string {
        return 'ListPanini2024PicturesEndpoint';
    }

    // public function getResponseField(): FieldTypes\Field {
    //     $panini_2024_picture_field = new FieldTypes\ObjectField([
    //         'export_as' => 'OlzPanini2024PictureData',
    //         'field_structure' => [
    //             'id' => new FieldTypes\IntegerField(['min_value' => 1]),
    //             'line1' => new FieldTypes\StringField(['allow_null' => false]),
    //             'line2' => new FieldTypes\StringField(['allow_null' => true]),
    //             'association' => new FieldTypes\StringField(['allow_null' => true]),
    //             'imgSrc' => new FieldTypes\StringField(['allow_null' => false]),
    //             'imgStyle' => new FieldTypes\StringField(['allow_null' => false]),
    //             'isLandscape' => new FieldTypes\BooleanField(['allow_null' => false]),
    //             'hasTop' => new FieldTypes\BooleanField(['allow_null' => false]),
    //         ],
    //     ]);
    //     return new FieldTypes\ArrayField([
    //         'item_field' => new FieldTypes\ObjectField(['field_structure' => [
    //             'data' => $panini_2024_picture_field,
    //         ]]),
    //     ]);
    // }

    // public function getRequestField(): FieldTypes\Field {
    //     $skill_filter = new FieldTypes\ChoiceField([
    //         'field_map' => [
    //             'idIs' => new FieldTypes\IntegerField(['min_value' => 1]),
    //             'page' => new FieldTypes\IntegerField(['min_value' => 1]),
    //         ],
    //         'allow_null' => true,
    //     ]);
    //     return new FieldTypes\ObjectField(['field_structure' => [
    //         'filter' => $skill_filter,
    //     ]]);
    // }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('panini2024');

        // TODO: Implement

        return [];
    }
}
