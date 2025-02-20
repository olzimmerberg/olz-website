<?php

namespace Olz\Karten\Endpoints;

use Olz\Entity\Karten\Karte;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * Note: `latitude` may be from -90.0 to 90.0, `longitude` from -180.0 to 180.0.
 *
 * @phpstan-type OlzKarteId int
 * @phpstan-type OlzKarteData array{
 *   kartennr?: ?int,
 *   name: non-empty-string,
 *   latitude?: ?float,
 *   longitude?: ?float,
 *   year?: ?int,
 *   scale?: ?non-empty-string,
 *   place?: ?non-empty-string,
 *   zoom?: ?int,
 *   kind?: ?OlzKarteKind,
 *   previewImageId?: ?non-empty-string,
 * }
 * @phpstan-type OlzKarteKind 'ol'|'stadt'|'scool'
 */
trait KarteEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzKarteData */
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
            'kind' => $this->getKindForApi($entity),
            'previewImageId' => $preview_image_id ? $preview_image_id : null,
        ];
    }

    /** @param OlzKarteData $input_data */
    public function updateEntityWithData(Karte $entity, array $input_data): void {
        $year = $input_data['year'] ?? null;
        $valid_year = $year ? strval($year) : null;
        $preview_image_id = $input_data['previewImageId'] ?? null;
        $valid_preview_image_id = $this->uploadUtils()->getValidUploadId($preview_image_id);

        $entity->setKartenNr($input_data['kartennr'] ?? null);
        $entity->setName($input_data['name']);
        $entity->setLatitude($input_data['latitude'] ?? null);
        $entity->setLongitude($input_data['longitude'] ?? null);
        $entity->setYear($valid_year);
        $entity->setScale($input_data['scale'] ?? null);
        $entity->setPlace($input_data['place'] ?? null);
        $entity->setZoom($input_data['zoom'] ?? null);
        $entity->setKind($input_data['kind'] ?? null);
        $entity->setPreviewImageId($valid_preview_image_id);
    }

    /** @param OlzKarteData $input_data */
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

    // ---

    /** @return ?OlzKarteKind */
    protected function getKindForApi(Karte $entity): ?string {
        switch ($entity->getKind()) {
            case 'ol': return 'ol';
            case 'stadt': return 'stadt';
            case 'scool': return 'scool';
            case null: return null;
            default: throw new \Exception("Unknown karten kind: {$entity->getKind()} ({$entity})");
        }
    }
}
