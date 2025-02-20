<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLabel;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzTerminLabelId int
 * @phpstan-type OlzTerminLabelData array{
 *   ident: non-empty-string,
 *   name: non-empty-string,
 *   details: string,
 *   icon?: ?non-empty-string,
 *   position?: ?int,
 *   imageIds: array<non-empty-string>,
 *   fileIds: array<non-empty-string>,
 * }
 */
trait TerminLabelEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzTerminLabelData */
    public function getEntityData(TerminLabel $entity): array {
        return [
            'ident' => $entity->getIdent() ? $entity->getIdent() : '-',
            'name' => $entity->getName() ? $entity->getName() : '-',
            'details' => $entity->getDetails() ?? '',
            'icon' => $entity->getIcon() ? $entity->getIcon() : null,
            'position' => $entity->getPosition(),
            'imageIds' => $entity->getStoredImageUploadIds(),
            'fileIds' => $entity->getStoredFileUploadIds(),
        ];
    }

    /** @param OlzTerminLabelData $input_data */
    public function updateEntityWithData(TerminLabel $entity, array $input_data): void {
        $valid_icon_file_id = $this->uploadUtils()->getValidUploadId($input_data['icon'] ?? null);

        $entity->setIdent($input_data['ident']);
        $entity->setName($input_data['name']);
        $entity->setDetails($input_data['details']);
        $entity->setIcon($valid_icon_file_id);
        $entity->setPosition($input_data['position'] ?? 0);
    }

    /** @param OlzTerminLabelData $input_data */
    public function persistUploads(TerminLabel $entity, array $input_data): void {
        $this->persistOlzImages($entity, $input_data['imageIds']);
        $this->persistOlzFiles($entity, $input_data['fileIds']);
        $icon = $input_data['icon'] ?? null;
        if ($icon) {
            $this->persistOlzFiles($entity, [$icon]);
        }
    }

    public function editUploads(TerminLabel $entity): void {
        $image_ids = $this->uploadUtils()->getStoredUploadIds("{$entity->getImagesPathForStorage()}img/");
        $this->editOlzImages($entity, $image_ids);
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): TerminLabel {
        $repo = $this->entityManager()->getRepository(TerminLabel::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }

    /** @return array<TerminLabel> */
    protected function listEntities(): array {
        $repo = $this->entityManager()->getRepository(TerminLabel::class);
        return $repo->findBy([], ['position' => 'ASC']);
    }
}
