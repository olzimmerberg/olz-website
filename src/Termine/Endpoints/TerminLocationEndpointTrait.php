<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait TerminLocationEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminLocationDataOrNull' : 'OlzTerminLocationData',
            'field_structure' => [
                'name' => new FieldTypes\StringField([]),
                'details' => new FieldTypes\StringField(['allow_empty' => true]),
                'latitude' => new FieldTypes\NumberField(['min_value' => -90, 'max_value' => 90]),
                'longitude' => new FieldTypes\NumberField(['min_value' => -180, 'max_value' => 180]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(TerminLocation $entity): array {
        return [
            'name' => $entity->getName(),
            'details' => $entity->getDetails() ?? '',
            'latitude' => $entity->getLatitude(),
            'longitude' => $entity->getLongitude(),
            'imageIds' => $entity->getImageIds() ?? [],
        ];
    }

    public function updateEntityWithData(TerminLocation $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $entity->setName($input_data['name']);
        $entity->setDetails($input_data['details']);
        $entity->setLatitude($input_data['latitude']);
        $entity->setLongitude($input_data['longitude']);
        $entity->setImageIds($valid_image_ids);
    }

    public function persistUploads(TerminLocation $entity): void {
        $this->persistOlzImages($entity, $entity->getImageIds());
    }

    public function editUploads(TerminLocation $entity): void {
        $this->editOlzImages($entity, $entity->getImageIds());
    }

    protected function getEntityById(int $id): TerminLocation {
        $repo = $this->entityManager()->getRepository(TerminLocation::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
