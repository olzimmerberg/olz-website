<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditDownloadEndpoint extends OlzEditEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'EditDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $download = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($download, null, 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $download_files_path = "{$data_path}files/downloads/{$download->getId()}/";
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
