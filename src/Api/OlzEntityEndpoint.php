<?php

namespace Olz\Api;

use Olz\Entity\Common\DataStorageInterface;
use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzEntityEndpoint extends OlzEndpoint {
    use \Psr\Log\LoggerAwareTrait;

    abstract public function usesExternalId(): bool;

    abstract public function getEntityDataField(bool $allow_null): FieldTypes\Field;

    protected function getIdField(bool $allow_null = false): FieldTypes\Field {
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
        // TODO: Generate default thumbnails.
        /*
         * Thumnails sollen schon beim Upload, und nicht wie bisher mittels `image_tools` generiert werden!
         *
         * z.B.
         * - `.../img/news/1234/thumb/abcdefghijklmnopqrstuvwx_default.jpg` (default = max. Grösse 128)
         * - `.../img/news/1234/thumb/abcdefghijklmnopqrstuvwx_160x120.jpg` (möglich für zukünftige Features)
         * - `.../img/news/1234/thumb/abcdefghijklmnopqrstuvwx_240.jpg` (möglich für zukünftige Features)
         * - `.../img/news/1234/thumb/9TsaBhb4DvkpIrhJt4kjvhrO_default.jpg`
         * - `.../img/news/1235/thumb/76ffQmgAiCRv1HOTLdumQJIS_default.jpg`
         * - `.../img/news/3/thumb/bAKg1ext1rH9_e0h3ky5vN0f_default.jpg`
         * - `.../img/news/3/thumb/LFm4w-0p1ItH0FVReqS2SU4M_default.jpg`
         * - etc.
         */
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
