<?php

namespace Olz\Service\Endpoints;

use Olz\Entity\Service\Download;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

trait DownloadEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzDownloadDataOrNull' : 'OlzDownloadData',
            'field_structure' => [
                'name' => new FieldTypes\StringField([]),
                'position' => new FieldTypes\IntegerField(['allow_null' => true]),
                'fileId' => new FieldTypes\StringField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(Download $entity): array {
        $data_path = $this->envUtils()->getDataPath();

        $one_file_id = null;
        $download_files_path = "{$data_path}files/downloads/{$entity->getId()}/";
        $files_path_entries = is_dir($download_files_path)
            ? scandir($download_files_path) : [];
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $one_file_id = $file_id;
            }
        }

        return [
            'name' => $entity->getName(),
            'position' => $entity->getPosition(),
            'fileId' => $one_file_id,
        ];
    }

    public function updateEntityWithData(Download $entity, array $input_data): void {
        $entity->setName($input_data['name']);
        $entity->setPosition(intval($input_data['position']));
        $entity->setFileId($input_data['fileId']);
    }

    public function persistUploads(Download $entity, array $input_data): void {
        $data_path = $this->envUtils()->getDataPath();

        $download_id = $entity->getId();

        $download_files_path = "{$data_path}files/downloads/{$download_id}/";
        if (!is_dir("{$download_files_path}")) {
            mkdir("{$download_files_path}", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads([$input_data['fileId']], $download_files_path);
    }
}