<?php

namespace Olz\Snippets\Endpoints;

use Olz\Entity\Snippets\Snippet;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzSnippetId int
 * @phpstan-type OlzSnippetData array{
 *   text: string,
 *   imageIds: array<non-empty-string>,
 *   fileIds: array<non-empty-string>,
 * }
 */
trait SnippetEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzSnippetData */
    public function getEntityData(Snippet $entity): array {
        return [
            'text' => $entity->getText() ?? '',
            'imageIds' => $entity->getStoredImageUploadIds(),
            'fileIds' => $entity->getStoredFileUploadIds(),
        ];
    }

    /** @param OlzSnippetData $input_data */
    public function updateEntityWithData(Snippet $entity, array $input_data): void {
        $entity->setText($input_data['text']);
    }

    /** @param OlzSnippetData $input_data */
    public function persistUploads(Snippet $entity, array $input_data): void {
        $this->persistOlzImages($entity, $input_data['imageIds']);
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(Snippet $entity): void {
        $image_ids = $this->uploadUtils()->getStoredUploadIds("{$entity->getImagesPathForStorage()}img/");
        $this->editOlzImages($entity, $image_ids);
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Snippet {
        $repo = $this->entityManager()->getRepository(Snippet::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            $has_access = $this->authUtils()->hasPermission("snippet_{$id}");
            if (!$has_access) {
                throw new HttpError(404, "Nicht gefunden.");
            }
            $entity = new Snippet();
            $this->entityUtils()->createOlzEntity($entity, ['onOff' => true]);
            $entity->setId($id);
            $entity->setText('');
            $this->entityManager()->persist($entity);
            $this->entityManager()->flush();
        }
        return $entity;
    }
}
