<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLabel;
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
                'shouldPromote' => new FieldTypes\BooleanField([]),
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
        $types_for_api = $this->getTypesForApi($entity->getLabels());

        $file_ids = $entity->getStoredFileUploadIds();

        return [
            'startTime' => $entity->getStartTime()?->format('H:i:s'),
            'durationSeconds' => $entity->getDurationSeconds(),
            'title' => $entity->getTitle() ?? '',
            'text' => $entity->getText() ?? '',
            'deadlineEarlierSeconds' => $entity->getDeadlineEarlierSeconds(),
            'deadlineTime' => $entity->getDeadlineTime()?->format('H:i:s'),
            'shouldPromote' => $entity->getShouldPromote(),
            'newsletter' => $entity->getNewsletter(),
            'types' => $types_for_api,
            'locationId' => $entity->getLocation()?->getId(),
            'imageIds' => $entity->getImageIds(),
            'fileIds' => $file_ids,
        ];
    }

    /** @param array<string, mixed> $input_data */
    public function updateEntityWithData(TerminTemplate $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $input_data['locationId']]);

        $entity->setStartTime($input_data['startTime'] ? new \DateTime($input_data['startTime']) : null);
        $entity->setDurationSeconds($input_data['durationSeconds']);
        $entity->setTitle($input_data['title']);
        $entity->setText($input_data['text']);
        $entity->setDeadlineEarlierSeconds($input_data['deadlineEarlierSeconds']);
        $entity->setDeadlineTime($input_data['deadlineTime'] ? new \DateTime($input_data['deadlineTime']) : null);
        if (count($valid_image_ids) > 0) {
            $entity->setShouldPromote($input_data['shouldPromote']);
        } else {
            $entity->setShouldPromote(false);
        }
        $entity->setNewsletter($input_data['newsletter']);
        $entity->clearLabels();
        foreach ($input_data['types'] as $ident) {
            $termin_label = $termin_label_repo->findOneBy(['ident' => $ident]);
            if (!$termin_label) {
                throw new HttpError(400, "No such TerminLabel: {$ident}");
            }
            $entity->addLabel($termin_label);
        }
        $entity->setLocation($termin_location);
        $entity->setImageIds($valid_image_ids);
    }

    /** @param array<string, mixed> $input_data */
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

    /**
     * @param iterable<TerminLabel> $labels
     *
     * @return array<string>
     */
    protected function getTypesForApi(?iterable $labels): array {
        $types_for_api = [];
        foreach ($labels as $label) {
            $types_for_api[] = $label->getIdent();
        }
        return $types_for_api;
    }
}
