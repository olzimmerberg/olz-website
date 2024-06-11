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
                'kartennr' => new FieldTypes\IntegerField(['allow_null' => true]),
                'name' => new FieldTypes\StringField([]),
                'latitude' => new FieldTypes\NumberField([
                    'min_value' => -90,
                    'max_value' => 90,
                    'allow_null' => true,
                ]),
                'longitude' => new FieldTypes\NumberField([
                    'min_value' => -180,
                    'max_value' => 180,
                    'allow_null' => true,
                ]),
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

    /** @return array<string, mixed> */
    public function getEntityData(Karte $entity): array {
        $name = $entity->getName();
        $latitude = $entity->getLatitude();
        $longitude = $entity->getLongitude();
        $year = $entity->getYear();
        $scale = $entity->getScale();
        $place = $entity->getPlace();
        $zoom = $entity->getZoom();
        $preview_image_id = $entity->getPreviewImageId();

        return [
            'kartennr' => $entity->getKartenNr() ?? null,
            'name' => $name ? $name : '-',
            'latitude' => $latitude ? floatval($latitude) : null,
            'longitude' => $longitude ? floatval($longitude) : null,
            'year' => $year ? intval($year) : null,
            'scale' => $scale ? $scale : null,
            'place' => $place ? $place : null,
            'zoom' => $zoom ? intval($zoom) : null,
            'kind' => $entity->getKind() ?? null,
            'previewImageId' => $preview_image_id ? $preview_image_id : null,
        ];
    }

    /** @param array<string, mixed> $input_data */
    public function updateEntityWithData(Karte $entity, array $input_data): void {
        $valid_preview_image_id = $this->uploadUtils()->getValidUploadId($input_data['previewImageId']);

        $entity->setKartenNr($input_data['kartennr']);
        $entity->setName($input_data['name']);
        $entity->setLatitude($input_data['latitude']);
        $entity->setLongitude($input_data['longitude']);
        $entity->setYear($input_data['year']);
        $entity->setScale($input_data['scale']);
        $entity->setPlace($input_data['place']);
        $entity->setZoom($input_data['zoom']);
        $entity->setKind($input_data['kind']);
        $entity->setPreviewImageId($valid_preview_image_id);
    }

    /** @param array<string, mixed> $input_data */
    public function persistUploads(Karte $entity, array $input_data): void {
        $this->persistOlzImages($entity, [$entity->getPreviewImageId()]);
    }

    public function editUploads(Karte $entity): void {
        $this->editOlzImages($entity, [$entity->getPreviewImageId()]);
    }

    protected function getEntityById(int $id): Karte {
        $repo = $this->entityManager()->getRepository(Karte::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
