<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Entity\OlzEntity;
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

        $types_for_api = $this->getTypesForApi($termin->getTypes() ?? '');

        $image_ids = $termin->getImageIds();
        $termin_img_path = "{$data_path}img/termine/{$entity_id}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$termin_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $file_ids = [];
        $termin_files_path = "{$data_path}files/termine/{$entity_id}/";
        if (!is_dir("{$termin_files_path}")) {
            mkdir("{$termin_files_path}", 0777, true);
        }
        $files_path_entries = scandir($termin_files_path);
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_ids[] = $file_id;
                $file_path = "{$termin_files_path}{$file_id}";
                $temp_path = "{$data_path}temp/{$file_id}";
                copy($file_path, $temp_path);
            }
        }

        return [
            'id' => $entity_id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => $termin->getOnOff() ? true : false,
            ],
            'data' => [
                'startDate' => $termin->getStartsOn()->format('Y-m-d'),
                'startTime' => $termin->getStartTime() ? $termin->getStartTime()->format('H:i:s') : null,
                'endDate' => $termin->getEndsOn() ? $termin->getEndsOn()->format('Y-m-d') : null,
                'endTime' => $termin->getEndTime() ? $termin->getEndTime()->format('H:i:s') : null,
                'title' => $termin->getTitle(),
                'text' => $termin->getText() ?? '',
                'link' => $termin->getLink() ?? '',
                'deadline' => $termin->getDeadline() ? $termin->getDeadline()->format('Y-m-d H:i:s') : null,
                'newsletter' => $termin->getNewsletter(),
                'solvId' => $termin->getSolvId() ? $termin->getSolvId() : null,
                'go2olId' => $termin->getGo2olId() ? $termin->getGo2olId() : null,
                'types' => $types_for_api,
                'coordinateX' => $termin->getCoordinateX(),
                'coordinateY' => $termin->getCoordinateY(),
                'imageIds' => $termin->getImageIds() ?? [],
                'fileIds' => $file_ids,
            ],
        ];
    }
}
