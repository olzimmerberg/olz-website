<?php

namespace Olz\Service\Endpoints;

use Olz\Entity\Service\Download;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzDownloadId int
 * @phpstan-type OlzDownloadData array{
 *   name: non-empty-string,
 *   position?: ?int,
 *   fileId?: ?non-empty-string,
 * }
 */
trait DownloadEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzDownloadData */
    public function getEntityData(Download $entity): array {
        $file_ids = $entity->getStoredFileUploadIds();
        return [
            'name' => $entity->getName(),
            'position' => $entity->getPosition(),
            'fileId' => $file_ids[0] ?? null,
        ];
    }

    /** @param OlzDownloadData $input_data */
    public function updateEntityWithData(Download $entity, array $input_data): void {
        $entity->setName($input_data['name']);
        $entity->setPosition(intval($input_data['position']));
        $entity->setFileId($input_data['fileId']);
    }

    /** @param OlzDownloadData $input_data */
    public function persistUploads(Download $entity, array $input_data): void {
        $this->persistOlzFiles($entity, [$input_data['fileId']]);
    }

    public function editUploads(Download $entity): void {
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Download {
        $repo = $this->entityManager()->getRepository(Download::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
