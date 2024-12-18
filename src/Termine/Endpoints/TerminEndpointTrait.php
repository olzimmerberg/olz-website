<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait TerminEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminDataOrNull' : 'OlzTerminData',
            'field_structure' => [
                'fromTemplateId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'startDate' => new FieldTypes\DateField(['allow_null' => false]),
                'startTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'endDate' => new FieldTypes\DateField(['allow_null' => true]),
                'endTime' => new FieldTypes\TimeField(['allow_null' => true]),
                'title' => new FieldTypes\StringField([]),
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'deadline' => new FieldTypes\DateTimeField(['allow_null' => true]),
                'shouldPromote' => new FieldTypes\BooleanField([]),
                'newsletter' => new FieldTypes\BooleanField([]),
                'solvId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'go2olId' => new FieldTypes\StringField(['allow_null' => true]),
                // TODO: Migrate to labels
                'types' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'locationId' => new FieldTypes\IntegerField(['allow_null' => true]),
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

    /** @return array<string, mixed> */
    public function getEntityData(Termin $entity): array {
        $types_for_api = $this->getTypesForApi($entity->getLabels());

        $file_ids = $entity->getStoredFileUploadIds();

        return [
            'fromTemplateId' => $entity->getFromTemplate()?->getId(),
            'startDate' => $entity->getStartDate()->format('Y-m-d'),
            'startTime' => $entity->getStartTime()?->format('H:i:s'),
            'endDate' => $entity->getEndDate()?->format('Y-m-d'),
            'endTime' => $entity->getEndTime()?->format('H:i:s'),
            'title' => $entity->getTitle(),
            'text' => $entity->getText() ?? '',
            'deadline' => $entity->getDeadline()?->format('Y-m-d H:i:s'),
            'shouldPromote' => $entity->getShouldPromote(),
            'newsletter' => $entity->getNewsletter(),
            'solvId' => $entity->getSolvId() ? $entity->getSolvId() : null,
            'go2olId' => $entity->getGo2olId() ? $entity->getGo2olId() : null,
            'types' => $types_for_api,
            'locationId' => $entity->getLocation()?->getId(),
            'coordinateX' => $entity->getCoordinateX(),
            'coordinateY' => $entity->getCoordinateY(),
            'imageIds' => $entity->getImageIds(),
            'fileIds' => $file_ids,
        ];
    }

    /** @param array<string, mixed> $input_data */
    public function updateEntityWithData(Termin $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_template = $termin_template_repo->findOneBy(['id' => $input_data['fromTemplateId']]);
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $input_data['locationId']]);

        $entity->setFromTemplate($termin_template);
        $entity->setStartDate(new \DateTime($input_data['startDate']));
        $entity->setStartTime($input_data['startTime'] ? new \DateTime($input_data['startTime']) : null);
        $entity->setEndDate($input_data['endDate'] ? new \DateTime($input_data['endDate']) : null);
        $entity->setEndTime($input_data['endTime'] ? new \DateTime($input_data['endTime']) : null);
        $entity->setTitle($input_data['title']);
        $entity->setText($input_data['text']);
        $entity->setDeadline($input_data['deadline'] ? new \DateTime($input_data['deadline']) : null);
        if (count($valid_image_ids) > 0) {
            $entity->setShouldPromote($input_data['shouldPromote']);
        } else {
            $entity->setShouldPromote(false);
        }
        $entity->setNewsletter($input_data['newsletter']);
        $entity->setSolvId($input_data['solvId']);
        $entity->setGo2olId($input_data['go2olId']);
        $entity->clearLabels();
        foreach ($input_data['types'] as $ident) {
            $termin_label = $termin_label_repo->findOneBy(['ident' => $ident]);
            if (!$termin_label) {
                throw new HttpError(400, "No such TerminLabel: {$ident}");
            }
            $entity->addLabel($termin_label);
        }
        $entity->setLocation($termin_location);
        $entity->setCoordinateX($input_data['coordinateX']);
        $entity->setCoordinateY($input_data['coordinateY']);
        $entity->setImageIds($valid_image_ids);
    }

    /** @param array<string, mixed> $input_data */
    public function persistUploads(Termin $entity, array $input_data): void {
        $this->persistOlzImages($entity, $entity->getImageIds());
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(Termin $entity): void {
        $this->editOlzImages($entity, $entity->getImageIds());
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Termin {
        $repo = $this->entityManager()->getRepository(Termin::class);
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
