<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditTerminEndpoint extends OlzEditEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'EditTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_ids = $termin->getImageIds();
        $termin_img_path = "{$data_path}img/termine/{$termin->getId()}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$termin_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $termin_files_path = "{$data_path}files/termine/{$termin->getId()}/";
        if (!is_dir("{$termin_files_path}")) {
            mkdir("{$termin_files_path}", 0777, true);
        }
        $files_path_entries = scandir($termin_files_path);
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_path = "{$termin_files_path}{$file_id}";
                $temp_path = "{$data_path}temp/{$file_id}";
                copy($file_path, $temp_path);
            }
        }

        return [
            'id' => $termin->getId(),
            'meta' => $termin->getMetaData(),
            'data' => $this->getEntityData($termin),
        ];
    }
}
