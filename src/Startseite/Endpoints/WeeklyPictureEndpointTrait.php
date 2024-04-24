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
            ],
            'allow_null' => $allow_null,
        ]);
    }

    // TODO: Implement once needed
    public function getEntityData(WeeklyPicture $entity): array {
        return [];
    }

    public function updateEntityWithData(WeeklyPicture $entity, array $input_data): void {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $valid_image_id = $this->uploadUtils()->getValidUploadId($input_data['imageId']);
        if ($valid_image_id === null) {
            throw new HttpError(400, "Kein gültiges Bild!");
        }

        $entity->setPublishedDate($now);
        $entity->setText($input_data['text']);
        $entity->setImageId($valid_image_id);
    }

    public function persistUploads(WeeklyPicture $entity): void {
        $data_path = $this->envUtils()->getDataPath();

        $weekly_picture_id = $entity->getId();
        $valid_image_id = $entity->getImageId();

        $weekly_picture_img_path = "{$data_path}img/weekly_picture/{$weekly_picture_id}/";
        mkdir("{$weekly_picture_img_path}img/", 0o777, true);
        mkdir("{$weekly_picture_img_path}thumb/", 0o777, true);
        $valid_image_ids = [$valid_image_id];
        $this->uploadUtils()->overwriteUploads($valid_image_ids, "{$weekly_picture_img_path}img/");
        // TODO: Generate default thumbnails.
    }
}
