<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditTerminTemplateEndpoint extends OlzEditEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'EditTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_template = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_template, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_ids = $termin_template->getImageIds();
        $termin_template_img_path = "{$data_path}img/termin_templates/{$termin_template->getId()}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$termin_template_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $termin_template_files_path = "{$data_path}files/termin_templates/{$termin_template->getId()}/";
        if (!is_dir("{$termin_template_files_path}")) {
            mkdir("{$termin_template_files_path}", 0777, true);
        }
        $files_path_entries = scandir($termin_template_files_path);
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_path = "{$termin_template_files_path}{$file_id}";
                $temp_path = "{$data_path}temp/{$file_id}";
                copy($file_path, $temp_path);
            }
        }

        return [
            'id' => $termin_template->getId(),
            'meta' => $termin_template->getMetaData(),
            'data' => $this->getEntityData($termin_template),
        ];
    }
}
