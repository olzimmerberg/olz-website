<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Utils\MapUtils;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzTerminLocationId int
 *
 * @phpstan-import-type OlzLocationCoordinates from MapUtils
 *
 * @phpstan-type OlzTerminLocationData array{
 *   name: non-empty-string,
 *   details: string,
 *   location: OlzLocationCoordinates,
 *   imageIds: array<non-empty-string>,
 * }
 */
trait TerminLocationEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzTerminLocationData */
    public function getEntityData(TerminLocation $entity): array {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($entity->getImageIds());

        return [
            'name' => $entity->getName() ?: '-',
            'details' => $entity->getDetails() ?? '',
            'location' => [
                'latitude' => $entity->getLatitude(),
                'longitude' => $entity->getLongitude(),
            ],
            'imageIds' => $valid_image_ids,
        ];
    }

    /** @param OlzTerminLocationData $input_data */
    public function updateEntityWithData(TerminLocation $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $entity->setName($input_data['name']);
        $entity->setDetails($input_data['details']);
        $entity->setLatitude($input_data['location']['latitude']);
        $entity->setLongitude($input_data['location']['longitude']);
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
