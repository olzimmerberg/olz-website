<?php

namespace Olz\Api;

use Olz\Entity\Common\DataStorageInterface;

/**
 * @phpstan-type OlzMetaData array{
 *   ownerUserId: ?int,
 *   ownerRoleId: ?int,
 *   onOff: bool,
 * }
 */
trait OlzEntityEndpointTrait {
    /** @param ?array<string> $image_ids */
    protected function persistOlzImages(DataStorageInterface $entity, ?array $image_ids): void {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_img_path = "{$data_path}img/{$entity_name}/{$entity_id}/";
        if (!is_dir("{$entity_img_path}img/")) {
            mkdir("{$entity_img_path}img/", 0o777, true);
        }
        if (!is_dir("{$entity_img_path}thumb/")) {
            mkdir("{$entity_img_path}thumb/", 0o777, true);
        }
        $this->uploadUtils()->overwriteUploads($image_ids, "{$entity_img_path}img/");
        $this->imageUtils()->generateThumbnails($image_ids ?? [], $entity_img_path);
    }

    /** @param ?array<string> $image_ids */
    protected function editOlzImages(DataStorageInterface $entity, ?array $image_ids): void {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_img_path = "{$data_path}img/{$entity_name}/{$entity_id}/";
        $this->uploadUtils()->editUploads($image_ids, "{$entity_img_path}img/");
    }

    /** @param ?array<string> $file_ids */
    protected function persistOlzFiles(DataStorageInterface $entity, ?array $file_ids): void {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_files_path = "{$data_path}files/{$entity_name}/{$entity_id}/";
        if (!is_dir("{$entity_files_path}")) {
            mkdir("{$entity_files_path}", 0o777, true);
        }
        $this->uploadUtils()->overwriteUploads($file_ids, $entity_files_path);
    }

    protected function editOlzFiles(DataStorageInterface $entity): void {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_files_path = "{$data_path}files/{$entity_name}/{$entity_id}/";
        if (!is_dir("{$entity_files_path}")) {
            mkdir("{$entity_files_path}", 0o777, true);
        }
        $file_ids = $this->uploadUtils()->getStoredUploadIds($entity_files_path);
        foreach ($file_ids as $file_id) {
            $file_path = "{$entity_files_path}{$file_id}";
            $temp_path = "{$data_path}temp/{$file_id}";
            copy($file_path, $temp_path);
        }
    }
}
