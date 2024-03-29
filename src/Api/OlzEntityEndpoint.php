<?php

namespace Olz\Api;

use Olz\Entity\Common\DataStorageInterface;
use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzEntityEndpoint extends OlzEndpoint {
    use \Psr\Log\LoggerAwareTrait;

    abstract public function usesExternalId(): bool;

    abstract public function getEntityDataField(bool $allow_null): FieldTypes\Field;

    protected function getIdField($allow_null = false) {
        if ($this->usesExternalId()) {
            return new FieldTypes\StringField([
                'allow_null' => $allow_null,
            ]);
        }
        return new FieldTypes\IntegerField([
            'allow_null' => $allow_null,
            'min_value' => 1,
        ]);
    }

    protected function persistOlzImages(DataStorageInterface $entity, ?array $image_ids) {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_img_path = "{$data_path}img/{$entity_name}/{$entity_id}/";
        if (!is_dir("{$entity_img_path}img/")) {
            mkdir("{$entity_img_path}img/", 0777, true);
        }
        if (!is_dir("{$entity_img_path}thumb/")) {
            mkdir("{$entity_img_path}thumb/", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($image_ids, "{$entity_img_path}img/");
        // TODO: Generate default thumbnails.
    }

    protected function editOlzImages(DataStorageInterface $entity, ?array $image_ids) {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_img_path = "{$data_path}img/{$entity_name}/{$entity_id}/";
        $this->uploadUtils()->editUploads($image_ids, "{$entity_img_path}img/");
    }

    protected function persistOlzFiles(DataStorageInterface $entity, array $file_ids) {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_files_path = "{$data_path}files/{$entity_name}/{$entity_id}/";
        if (!is_dir("{$entity_files_path}")) {
            mkdir("{$entity_files_path}", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($file_ids, $entity_files_path);
    }

    protected function editOlzFiles(DataStorageInterface $entity) {
        $data_path = $this->envUtils()->getDataPath();
        $entity_name = $entity::getEntityNameForStorage();
        $entity_id = $entity->getEntityIdForStorage();

        $entity_files_path = "{$data_path}files/{$entity_name}/{$entity_id}/";
        if (!is_dir("{$entity_files_path}")) {
            mkdir("{$entity_files_path}", 0777, true);
        }
        $file_ids = $this->uploadUtils()->getStoredUploadIds($entity_files_path);
        foreach ($file_ids as $file_id) {
            $file_path = "{$entity_files_path}{$file_id}";
            $temp_path = "{$data_path}temp/{$file_id}";
            copy($file_path, $temp_path);
        }
    }
}
