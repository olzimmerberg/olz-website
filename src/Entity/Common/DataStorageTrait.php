<?php

namespace Olz\Entity\Common;

use Olz\Utils\WithUtilsTrait;

trait DataStorageTrait {
    use WithUtilsTrait;

    public function getStoredImageUploadIds(): array {
        $img_path = $this->getImagesPathForStorage();
        return $this->uploadUtils()->getStoredUploadIds("{$img_path}img/");
    }

    public function getStoredFileUploadIds(): array {
        return $this->uploadUtils()->getStoredUploadIds($this->getFilesPathForStorage());
    }

    public function getImagesPathForStorage(): string {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $this::getEntityNameForStorage();
        $entity_id = $this->getEntityIdForStorage();
        return "{$data_path}img/{$entity_name}/{$entity_id}/";
    }

    public function getFilesPathForStorage(): string {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $this::getEntityNameForStorage();
        $entity_id = $this->getEntityIdForStorage();
        return "{$data_path}files/{$entity_name}/{$entity_id}/";
    }
}
