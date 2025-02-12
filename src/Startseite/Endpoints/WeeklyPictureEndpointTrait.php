<?php

namespace Olz\Startseite\Endpoints;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDate;

/**
 * @phpstan-type OlzWeeklyPictureId int
 * @phpstan-type OlzWeeklyPictureData array{
 *   text: string,
 *   imageId: non-empty-string,
 *   publishedDate?: ?IsoDate,
 * }
 */
trait WeeklyPictureEndpointTrait {
    use WithUtilsTrait;

    public function configureWeeklyPictureEndpointTrait(): void {
        $this->phpStanUtils->registerApiObject(IsoDate::class);
    }

    /** @return OlzWeeklyPictureData */
    public function getEntityData(WeeklyPicture $entity): array {
        return [
            'text' => $entity->getText() ?? '',
            'imageId' => $entity->getImageId() ? $entity->getImageId() : '-',
            'publishedDate' => IsoDate::fromDateTime($entity->getPublishedDate()),
        ];
    }

    /** @param OlzWeeklyPictureData $input_data */
    public function updateEntityWithData(WeeklyPicture $entity, array $input_data): void {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $published_date = $input_data['publishedDate'] ?? $now;

        $valid_image_id = $this->uploadUtils()->getValidUploadId($input_data['imageId']);
        if ($valid_image_id === null) {
            throw new HttpError(400, "Kein gültiges Bild!");
        }

        $entity->setPublishedDate($published_date);
        $entity->setText($input_data['text']);
        $entity->setImageId($valid_image_id);
    }

    public function persistUploads(WeeklyPicture $entity): void {
        $this->persistOlzImages($entity, [$entity->getImageId()]);
    }

    public function editUploads(WeeklyPicture $entity): void {
        $this->editOlzImages($entity, [$entity->getImageId()]);
    }

    protected function getEntityById(int $id): WeeklyPicture {
        $news_repo = $this->entityManager()->getRepository(WeeklyPicture::class);
        $entity = $news_repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
