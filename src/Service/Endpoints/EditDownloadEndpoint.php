<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Service\Download;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class EditDownloadEndpoint extends OlzEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'EditDownloadEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $download_repo = $this->entityManager()->getRepository(Download::class);
        $download = $download_repo->findOneBy(['id' => $entity_id]);

        if (!$download) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($download, null, 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $download_files_path = "{$data_path}files/downloads/{$entity_id}/";
        if (!is_dir("{$download_files_path}")) {
            mkdir("{$download_files_path}", 0777, true);
        }
        $files_path_entries = scandir($download_files_path);
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_path = "{$download_files_path}{$file_id}";
                $temp_path = "{$data_path}temp/{$file_id}";
                copy($file_path, $temp_path);
            }
        }

        return [
            'id' => $download->getId(),
            'meta' => $download->getMetaData(),
            'data' => $this->getEntityData($download),
        ];
    }
}
