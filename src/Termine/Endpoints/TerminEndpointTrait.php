<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Users\User;
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
 *   organizerUserId: ?int,
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

    /** @return OlzTerminData */
    public function getEntityData(Termin $entity): array {
        $types_for_api = $this->getTypesForApi($entity->getLabels());

        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($entity->getImageIds());
        $file_ids = $entity->getStoredFileUploadIds();

        return [
            'fromTemplateId' => $entity->getFromTemplate()?->getId(),
            'startDate' => IsoDate::fromDateTime($entity->getStartDate()),
            'startTime' => IsoTime::fromDateTime($entity->getStartTime()),
            'endDate' => IsoDate::fromDateTime($entity->getEndDate()),
            'endTime' => IsoTime::fromDateTime($entity->getEndTime()),
            'title' => $entity->getTitle() ?: '-',
            'text' => $entity->getText() ?? '',
            'organizerUserId' => $entity->getOrganizerUser()?->getId(),
            'deadline' => IsoDateTime::fromDateTime($entity->getDeadline()),
            'shouldPromote' => $entity->getShouldPromote(),
            'newsletter' => $entity->getNewsletter(),
            'solvId' => $entity->getSolvId() ?: null,
            'go2olId' => $entity->getGo2olId() ?: null,
            'types' => $types_for_api,
            'locationId' => $entity->getLocation()?->getId(),
            'coordinateX' => $entity->getCoordinateX(),
            'coordinateY' => $entity->getCoordinateY(),
            'imageIds' => $valid_image_ids,
            'fileIds' => $file_ids,
        ];
    }

    /** @param OlzTerminData $input_data */
    public function updateEntityWithData(Termin $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $from_template_id = $input_data['fromTemplateId'] ?? null;
        $termin_template = $termin_template_repo->findOneBy(['id' => $from_template_id]);
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        $organizer_user_id = $input_data['organizerUserId'] ?? null;
        $organizer_user = $user_repo->findOneBy(['id' => $organizer_user_id]);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $location_id = $input_data['locationId'] ?? null;
        $termin_location = $termin_location_repo->findOneBy(['id' => $location_id]);

        $entity->setFromTemplate($termin_template);
        $entity->setStartDate($input_data['startDate'] ?? new \DateTime());
        $entity->setStartTime($input_data['startTime'] ?? null);
        $entity->setEndDate($input_data['endDate'] ?? null);
        $entity->setEndTime($input_data['endTime'] ?? null);
        $entity->setTitle($input_data['title'] ?? null);
        $entity->setText($input_data['text']);
        $entity->setOrganizerUser($organizer_user);
        $entity->setDeadline($input_data['deadline'] ?? null);
        if (count($valid_image_ids) > 0) {
            $entity->setShouldPromote($input_data['shouldPromote']);
        } else {
            $entity->setShouldPromote(false);
        }
        $entity->setNewsletter($input_data['newsletter']);
        $entity->setSolvId($input_data['solvId'] ?? null);
        $entity->setGo2olId($input_data['go2olId'] ?? null);
        $entity->clearLabels();
        foreach ($input_data['types'] as $ident) {
            $termin_label = $termin_label_repo->findOneBy(['ident' => $ident]);
            if (!$termin_label) {
                throw new HttpError(400, "No such TerminLabel: {$ident}");
            }
            $entity->addLabel($termin_label);
        }
        $entity->setLocation($termin_location);
        $entity->setCoordinateX($input_data['coordinateX'] ?? null);
        $entity->setCoordinateY($input_data['coordinateY'] ?? null);
        $entity->setImageIds($valid_image_ids);

        if ($entity->getSolvId() !== null) {
            $this->termineUtils()->updateTerminFromSolvEvent($entity);
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
     * @param ?iterable<TerminLabel> $labels
     *
     * @return array<non-empty-string>
     */
    protected function getTypesForApi(?iterable $labels): array {
        $types_for_api = [];
        foreach ($labels ?? [] as $label) {
            $ident = $label->getIdent();
            if ($ident) {
                $types_for_api[] = $ident;
            }
        }
        return $types_for_api;
    }
}
