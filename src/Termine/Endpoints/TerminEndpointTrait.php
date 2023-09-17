<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

trait TerminEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminDataOrNull' : 'OlzTerminData',
            'field_structure' => [
                'startDate' => new FieldTypes\DateField(['allow_null' => false]),
                'startTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'endDate' => new FieldTypes\DateField(['allow_null' => true]),
                'endTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'title' => new FieldTypes\StringField([]),
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'link' => new FieldTypes\StringField(['allow_empty' => true]),
                'deadline' => new FieldTypes\DateTimeField(['allow_null' => true]),
                'newsletter' => new FieldTypes\BooleanField([]),
                'solvId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'go2olId' => new FieldTypes\StringField(['allow_null' => true]),
                'types' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'coordinateX' => new FieldTypes\IntegerField(['allow_null' => true]),
                'coordinateY' => new FieldTypes\IntegerField(['allow_null' => true]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'fileIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(Termin $entity): array {
        $data_path = $this->envUtils()->getDataPath();

        $types_for_api = $this->getTypesForApi($entity->getTypes() ?? '');

        $file_ids = [];
        $termin_files_path = "{$data_path}files/termine/{$entity->getId()}/";
        $files_path_entries = is_dir($termin_files_path)
            ? scandir($termin_files_path) : [];
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_ids[] = $file_id;
            }
        }

        return [
            'startDate' => $entity->getStartsOn()->format('Y-m-d'),
            'startTime' => $entity->getStartTime() ? $entity->getStartTime()->format('H:i:s') : null,
            'endDate' => $entity->getEndsOn() ? $entity->getEndsOn()->format('Y-m-d') : null,
            'endTime' => $entity->getEndTime() ? $entity->getEndTime()->format('H:i:s') : null,
            'title' => $entity->getTitle(),
            'text' => $entity->getText() ?? '',
            'link' => $entity->getLink() ?? '',
            'deadline' => $entity->getDeadline() ? $entity->getDeadline()->format('Y-m-d H:i:s') : null,
            'newsletter' => $entity->getNewsletter(),
            'solvId' => $entity->getSolvId() ? $entity->getSolvId() : null,
            'go2olId' => $entity->getGo2olId() ? $entity->getGo2olId() : null,
            'types' => $types_for_api,
            'coordinateX' => $entity->getCoordinateX(),
            'coordinateY' => $entity->getCoordinateY(),
            'imageIds' => $entity->getImageIds() ?? [],
            'fileIds' => $file_ids,
        ];
    }

    public function updateEntityWithData(Termin $entity, array $input_data): void {
        $types_for_db = $this->getTypesForDb($input_data['types']);
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $entity->setStartsOn(new \DateTime($input_data['startDate']));
        $entity->setStartTime($input_data['startTime'] ? new \DateTime($input_data['startTime']) : null);
        $entity->setEndsOn($input_data['endDate'] ? new \DateTime($input_data['endDate']) : null);
        $entity->setEndTime($input_data['endTime'] ? new \DateTime($input_data['endTime']) : null);
        $entity->setTitle($input_data['title']);
        $entity->setText($input_data['text']);
        $entity->setLink($input_data['link']);
        $entity->setDeadline($input_data['deadline'] ? new \DateTime($input_data['deadline']) : null);
        $entity->setNewsletter($input_data['newsletter']);
        $entity->setSolvId($input_data['solvId']);
        $entity->setGo2olId($input_data['go2olId']);
        $entity->setTypes($types_for_db);
        $entity->setCoordinateX($input_data['coordinateX']);
        $entity->setCoordinateY($input_data['coordinateY']);
        $entity->setImageIds($valid_image_ids);
    }

    public function persistUploads(Termin $entity, array $input_data): void {
        $data_path = $this->envUtils()->getDataPath();

        $termin_id = $entity->getId();
        $valid_image_ids = $entity->getImageIds();

        $termin_img_path = "{$data_path}img/termine/{$termin_id}/";
        if (!is_dir("{$termin_img_path}img/")) {
            mkdir("{$termin_img_path}img/", 0777, true);
        }
        if (!is_dir("{$termin_img_path}thumb/")) {
            mkdir("{$termin_img_path}thumb/", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($valid_image_ids, "{$termin_img_path}img/");
        // TODO: Generate default thumbnails.

        $termin_files_path = "{$data_path}files/termine/{$termin_id}/";
        if (!is_dir("{$termin_files_path}")) {
            mkdir("{$termin_files_path}", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($input_data['fileIds'], $termin_files_path);
    }

    // ---

    protected function getTypesForDb($types) {
        return ' '.implode(' ', $types ?? []).' ';
    }

    protected function getTypesForApi($types) {
        $types_string = $types ?? '';
        $types_for_api = [];
        foreach (explode(' ', $types_string) as $type) {
            if (trim($type) != '') {
                $types_for_api[] = trim($type);
            }
        }
        return $types_for_api;
    }
}
