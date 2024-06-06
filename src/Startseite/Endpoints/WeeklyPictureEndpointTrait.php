<?php

namespace Olz\Startseite\Endpoints;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait WeeklyPictureEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzWeeklyPictureDataOrNull' : 'OlzWeeklyPictureData',
            'field_structure' => [
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'imageId' => new FieldTypes\StringField([]),
                'publishedDate' => new FieldTypes\DateField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    /** @return array<string, mixed> */
    public function getEntityData(WeeklyPicture $entity): array {
        return [
            'text' => $entity->getText() ?? '',
            'imageId' => $entity->getImageId() ? $entity->getImageId() : '-',
            'publishedDate' => $entity->getPublishedDate()?->format('Y-m-d'),
        ];
    }

    /** @param array<string, mixed> $input_data */
    public function updateEntityWithData(WeeklyPicture $entity, array $input_data): void {
        $iso_now = $this->dateUtils()->getIsoNow();
        $published_date = new \DateTime($input_data['publishedDate'] ?? $iso_now);

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
