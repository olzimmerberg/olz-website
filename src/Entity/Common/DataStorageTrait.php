<?php

namespace Olz\Entity\Common;

use Olz\Utils\WithUtilsTrait;

trait DataStorageTrait {
    use WithUtilsTrait;

    /** @return array<non-empty-string> */
    public function getStoredImageUploadIds(): array {
        $img_path = $this->getImagesPathForStorage();
        return $this->uploadUtils()->getStoredUploadIds("{$img_path}img/");
    }

    /** @return array<non-empty-string> */
    public function getStoredFileUploadIds(): array {
        return $this->uploadUtils()->getStoredUploadIds($this->getFilesPathForStorage());
    }

    public function replaceImagePaths(string $html): string {
        $entity_name = $this::getEntityNameForStorage();
        $entity_id = $this->getEntityIdForStorage();
        $upload_ids = $this->getStoredImageUploadIds();
        foreach ($upload_ids as $upload_id) {
            $search = "<img src=\"./{$upload_id}\" alt=\"\" />";
            $replace = $this->imageUtils()->olzImage($entity_name, $entity_id, $upload_id, 110, 'image');
            $html = str_replace($search, $replace, $html);
        }
        return $html;
    }

    public function replaceFilePaths(string $html): string {
        $data_path = $this->envUtils()->getDataPath();
        $data_href = $this->envUtils()->getDataHref();
        $entity_path = $this->getEntityPathForStorage();
        $upload_ids = $this->getStoredFileUploadIds();
        foreach ($upload_ids as $upload_id) {
            $file_path = "{$data_path}files/{$entity_path}{$upload_id}";
            $file_href = "{$data_href}files/{$entity_path}{$upload_id}";
            $filemtime = is_file($file_path) ? filemtime($file_path) : false;
            $modified = is_int($filemtime) ? date('Y-m-d_H-i-s', $filemtime) : '';
            $search = "\"./{$upload_id}\"";
            $replace = "\"{$file_href}?modified={$modified}\"";
            $html = str_replace($search, $replace, $html);
        }
        return $html;
    }

    public function getFileHref(string $upload_id): string {
        $data_path = $this->envUtils()->getDataPath();
        $data_href = $this->envUtils()->getDataHref();
        $entity_path = $this->getEntityPathForStorage();
        $file_path = "{$data_path}files/{$entity_path}{$upload_id}";
        $file_href = "{$data_href}files/{$entity_path}{$upload_id}";
        $filemtime = is_file($file_path) ? filemtime($file_path) : false;
        $modified = is_int($filemtime) ? date('Y-m-d_H-i-s', $filemtime) : '';
        return "{$file_href}?modified={$modified}";
    }

    /** @return non-empty-string */
    public function getImagesPathForStorage(): string {
        $data_path = $this->envUtils()->getDataPath();
        $entity_path = $this->getEntityPathForStorage();
        return "{$data_path}img/{$entity_path}";
    }

    /** @return non-empty-string */
    public function getFilesPathForStorage(): string {
        $data_path = $this->envUtils()->getDataPath();
        $entity_path = $this->getEntityPathForStorage();
        return "{$data_path}files/{$entity_path}";
    }

    /** @return non-empty-string */
    public function getEntityPathForStorage(): string {
        $entity_name = $this::getEntityNameForStorage();
        $entity_id = $this->getEntityIdForStorage();
        return "{$entity_name}/{$entity_id}/";
    }
}
