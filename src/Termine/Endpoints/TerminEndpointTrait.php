<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDate;
use PhpTypeScriptApi\PhpStan\IsoDateTime;
use PhpTypeScriptApi\PhpStan\IsoTime;

/**
 * @phpstan-type OlzTerminId int
 * @phpstan-type OlzTerminData array{
 *   fromTemplateId?: ?int,
 *   startDate?: ?IsoDate,
 *   startTime?: ?IsoTime,
 *   endDate?: ?IsoDate,
 *   endTime?: ?IsoTime,
 *   title?: ?non-empty-string,
 *   text: string,
 *   deadline?: ?IsoDateTime,
 *   shouldPromote: bool,
 *   newsletter: bool,
 *   solvId?: ?int,
 *   go2olId?: ?non-empty-string,
 *   types: array<non-empty-string>,
 *   locationId?: ?int,
 *   coordinateX?: ?int,
 *   coordinateY?: ?int,
 *   imageIds: array<non-empty-string>,
 *   fileIds: array<non-empty-string>,
 * }
 */
trait TerminEndpointTrait {
    use WithUtilsTrait;

    public function configureTerminEndpointTrait(): void {
        $this->phpStanUtils->registerApiObject(IsoDate::class);
        $this->phpStanUtils->registerApiObject(IsoTime::class);
        $this->phpStanUtils->registerApiObject(IsoDateTime::class);
    }

    /** @return OlzTerminData */
    public function getEntityData(Termin $entity): array {
        $types_for_api = $this->getTypesForApi($entity->getLabels());

        $file_ids = $entity->getStoredFileUploadIds();

        return [
            'fromTemplateId' => $entity->getFromTemplate()?->getId(),
            'startDate' => IsoDate::fromDateTime($entity->getStartDate()),
            'startTime' => IsoTime::fromDateTime($entity->getStartTime()),
            'endDate' => IsoDate::fromDateTime($entity->getEndDate()),
            'endTime' => IsoTime::fromDateTime($entity->getEndTime()),
            'title' => $entity->getTitle(),
            'text' => $entity->getText() ?? '',
            'deadline' => IsoDateTime::fromDateTime($entity->getDeadline()),
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

    /** @param OlzTerminData $input_data */
    public function updateEntityWithData(Termin $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_template = $termin_template_repo->findOneBy(['id' => $input_data['fromTemplateId']]);
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $input_data['locationId']]);

        $entity->setFromTemplate($termin_template);
        $entity->setStartDate($input_data['startDate'] ?? new \DateTime());
        $entity->setStartTime($input_data['startTime']);
        $entity->setEndDate($input_data['endDate']);
        $entity->setEndTime($input_data['endTime']);
        $entity->setTitle($input_data['title']);
        $entity->setText($input_data['text']);
        $entity->setDeadline($input_data['deadline']);
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

        if ($entity->getSolvId() !== null) {
            $repo = $this->entityManager()->getRepository(Termin::class);
            $repo->updateTerminFromSolvEvent($entity);
        }
    }

    /** @param OlzTerminData $input_data */
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
