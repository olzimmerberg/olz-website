<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait TerminTemplateEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminTemplateDataOrNull' : 'OlzTerminTemplateData',
            'field_structure' => [
                'startTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'durationSeconds' => new FieldTypes\IntegerField(['allow_null' => true]),
                'title' => new FieldTypes\StringField(['allow_empty' => true]),
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'deadlineEarlierSeconds' => new FieldTypes\IntegerField(['allow_null' => true]),
                'deadlineTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'newsletter' => new FieldTypes\BooleanField(['allow_null' => false]),
                // TODO: Migrate to labels
                'types' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'locationId' => new FieldTypes\IntegerField(['allow_null' => true]),
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

    /** @return array<string, mixed> */
    public function getEntityData(TerminTemplate $entity): array {
        $types_for_api = $this->getTypesForApi($entity->getTypes() ?? '');

        $file_ids = $entity->getStoredFileUploadIds();

        return [
            'startTime' => $entity->getStartTime()?->format('H:i:s'),
            'durationSeconds' => $entity->getDurationSeconds(),
            'title' => $entity->getTitle() ?? '',
            'text' => $entity->getText() ?? '',
            'deadlineEarlierSeconds' => $entity->getDeadlineEarlierSeconds(),
            'deadlineTime' => $entity->getDeadlineTime()?->format('H:i:s'),
            'newsletter' => $entity->getNewsletter(),
            'types' => $types_for_api,
            'locationId' => $entity->getLocation()?->getId(),
            'imageIds' => $entity->getImageIds() ?? [],
            'fileIds' => $file_ids,
        ];
    }

    public function updateEntityWithData(TerminTemplate $entity, array $input_data): void {
        $types_for_db = $this->getTypesForDb($input_data['types']);
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $input_data['locationId']]);

        $entity->setStartTime($input_data['startTime'] ? new \DateTime($input_data['startTime']) : null);
        $entity->setDurationSeconds($input_data['durationSeconds']);
        $entity->setTitle($input_data['title']);
        $entity->setText($input_data['text']);
        $entity->setDeadlineEarlierSeconds($input_data['deadlineEarlierSeconds']);
        $entity->setDeadlineTime($input_data['deadlineTime'] ? new \DateTime($input_data['deadlineTime']) : null);
        $entity->setNewsletter($input_data['newsletter']);
        $entity->setTypes($types_for_db);
        $entity->setLocation($termin_location);
        $entity->setImageIds($valid_image_ids);
    }

    public function persistUploads(TerminTemplate $entity, array $input_data): void {
        $this->persistOlzImages($entity, $entity->getImageIds());
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(TerminTemplate $entity): void {
        $this->editOlzImages($entity, $entity->getImageIds());
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): TerminTemplate {
        $repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
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
