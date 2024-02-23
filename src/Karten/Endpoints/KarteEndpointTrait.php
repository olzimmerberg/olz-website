<?php

namespace Olz\Karten\Endpoints;

use Olz\Entity\Karten\Karte;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait KarteEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzKarteDataOrNull' : 'OlzKarteData',
            'field_structure' => [
                'position' => new FieldTypes\IntegerField([]),
                'kartennr' => new FieldTypes\IntegerField(['allow_null' => true]),
                'name' => new FieldTypes\StringField([]),
                'centerX' => new FieldTypes\IntegerField(['allow_null' => true]),
                'centerY' => new FieldTypes\IntegerField(['allow_null' => true]),
                'year' => new FieldTypes\IntegerField(['allow_null' => true]),
                'scale' => new FieldTypes\StringField(['allow_null' => true]),
                'place' => new FieldTypes\StringField(['allow_null' => true]),
                'zoom' => new FieldTypes\IntegerField(['allow_null' => true]),
                'kind' => new FieldTypes\EnumField([
                    'export_as' => 'OlzKarteKind',
                    'allow_null' => true,
                    'allowed_values' => ['ol', 'stadt', 'scool'],
                ]),
                'previewImageId' => new FieldTypes\StringField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(Karte $entity): array {
        $name = $entity->getName();
        $scale = $entity->getScale();
        $place = $entity->getPlace();
        $preview_image_id = $entity->getPreviewImageId();

        return [
            'position' => $entity->getPosition() ?? 0,
            'kartennr' => $entity->getKartenNr() ?? null,
            'name' => $name ? $name : '-',
            'centerX' => $entity->getCenterX() ?? null,
            'centerY' => $entity->getCenterY() ?? null,
            'year' => $entity->getYear() ?? null,
            'scale' => $scale ? $scale : null,
            'place' => $place ? $place : null,
            'zoom' => $entity->getZoom() ?? null,
            'kind' => $entity->getKind() ?? null,
            'previewImageId' => $preview_image_id ? $preview_image_id : null,
        ];
    }

    public function updateEntityWithData(Karte $entity, array $input_data): void {
        $valid_preview_image_id = $this->uploadUtils()->getValidUploadId($input_data['previewImageId']);

        $entity->setPosition($input_data['position']);
        $entity->setKartenNr($input_data['kartennr']);
        $entity->setName($input_data['name']);
        $entity->setCenterX($input_data['centerX']);
        $entity->setCenterY($input_data['centerY']);
        $entity->setYear($input_data['year']);
        $entity->setScale($input_data['scale']);
        $entity->setPlace($input_data['place']);
        $entity->setZoom($input_data['zoom']);
        $entity->setKind($input_data['kind']);
        $entity->setPreviewImageId($valid_preview_image_id);
    }

    public function persistUploads(Karte $entity, array $input_data): void {
        $data_path = $this->envUtils()->getDataPath();

        $karte_id = $entity->getId();
        $valid_preview_image_id = $entity->getPreviewImageId();

        $karte_img_path = "{$data_path}img/karten/{$karte_id}/";
        if (!is_dir("{$karte_img_path}img/")) {
            mkdir("{$karte_img_path}img/", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads([$valid_preview_image_id], "{$karte_img_path}img/");
        // TODO: Generate default thumbnails.
    }

    protected function getEntityById(int $id): Karte {
        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karte = $karten_repo->findOneBy(['id' => $id]);
        if (!$karte) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $karte;
    }
}
