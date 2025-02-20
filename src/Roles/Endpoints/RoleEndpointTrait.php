<?php

namespace Olz\Roles\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzRoleId int
 * @phpstan-type OlzRoleData array{
 *   username: non-empty-string,
 *   name: non-empty-string,
 *   title?: ?non-empty-string,
 *   description: string,
 *   guide: string,
 *   imageIds: array<non-empty-string>,
 *   fileIds: array<non-empty-string>,
 *   parentRole?: ?int<1, max>,
 *   indexWithinParent?: ?int<0, max>,
 *   featuredIndex?: ?int,
 *   canHaveChildRoles: bool,
 * }
 */
trait RoleEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzRoleData */
    public function getEntityData(Role $entity): array {
        return [
            'username' => $entity->getUsername() ? $entity->getUsername() : '-',
            'name' => $entity->getName() ? $entity->getName() : '-',
            'title' => $entity->getTitle() ? $entity->getTitle() : null,
            'description' => $entity->getDescription(),
            'guide' => $entity->getGuide(),
            'imageIds' => $entity->getStoredImageUploadIds(),
            'fileIds' => $entity->getStoredFileUploadIds(),
            'parentRole' => $this->getParentRoleId($entity),
            'indexWithinParent' => ($entity->getIndexWithinParent() ?? -1) < 0 ? null : $entity->getIndexWithinParent(),
            'featuredIndex' => $entity->getFeaturedIndex() ?? null,
            'canHaveChildRoles' => $entity->getCanHaveChildRoles(),
        ];
    }

    /** @param OlzRoleData $input_data */
    public function updateEntityWithData(Role $entity, array $input_data): void {
        $this->updateEntityWithNonParentData($entity, $input_data);
        $entity->setParentRoleId($input_data['parentRole'] ?? null);
        $entity->setIndexWithinParent($input_data['indexWithinParent'] ?? null);
        $entity->setFeaturedIndex($input_data['featuredIndex'] ?? null);
        $entity->setCanHaveChildRoles($input_data['canHaveChildRoles']);
    }

    /** @param OlzRoleData $input_data */
    public function updateEntityWithNonParentData(Role $entity, array $input_data): void {
        $entity->setUsername($input_data['username']);
        $entity->setName($input_data['name']);
        $entity->setTitle($input_data['title'] ?? null);
        $entity->setDescription($input_data['description']);
        $entity->setGuide($input_data['guide']);
    }

    /** @param OlzRoleData $input_data */
    public function persistUploads(Role $entity, array $input_data): void {
        $this->persistOlzImages($entity, $input_data['imageIds']);
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(Role $entity): void {
        $image_ids = $this->uploadUtils()->getStoredUploadIds("{$entity->getImagesPathForStorage()}img/");
        $this->editOlzImages($entity, $image_ids);
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Role {
        $repo = $this->entityManager()->getRepository(Role::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }

    // ---

    /** @return ?int<1, max> */
    protected function getParentRoleId(Role $entity): ?int {
        $number = $entity->getParentRoleId();
        if ($number === null) {
            return null;
        }
        if ($number < 1) {
            throw new \Exception("Invalid parent role ID: {$number} ({$entity})");
        }
        return $number;
    }
}
