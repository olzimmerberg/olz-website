<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class EditTerminEndpoint extends OlzEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'EditTerminEndpoint';
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
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneBy(['id' => $entity_id]);

        if (!$termin) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($termin, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_ids = $termin->getImageIds();
        $termin_img_path = "{$data_path}img/termine/{$entity_id}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$termin_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $termin_files_path = "{$data_path}files/termine/{$entity_id}/";
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
