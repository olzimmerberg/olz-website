<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

trait TerminLocationEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminLocationDataOrNull' : 'OlzTerminLocationData',
            'field_structure' => [
                'name' => new FieldTypes\StringField([]),
                'details' => new FieldTypes\StringField(['allow_empty' => true]),
                'latitude' => new FieldTypes\NumberField(['min_value' => -90, 'max_value' => 90]),
                'longitude' => new FieldTypes\NumberField(['min_value' => -180, 'max_value' => 180]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(TerminLocation $entity): array {
        return [
            'name' => $entity->getName(),
            'details' => $entity->getDetails() ?? '',
            'latitude' => $entity->getLatitude(),
            'longitude' => $entity->getLongitude(),
            'imageIds' => $entity->getImageIds() ?? [],
        ];
    }

    public function updateEntityWithData(TerminLocation $entity, array $input_data): void {
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $entity->setName($input_data['name']);
        $entity->setDetails($input_data['details']);
        $entity->setLatitude($input_data['latitude']);
        $entity->setLongitude($input_data['longitude']);
        $entity->setImageIds($valid_image_ids);
    }

    public function persistUploads(TerminLocation $entity): void {
        $data_path = $this->envUtils()->getDataPath();

        $termin_location_id = $entity->getId();
        $valid_image_ids = $entity->getImageIds();

        $termin_location_img_path = "{$data_path}img/termin_locations/{$termin_location_id}/";
        if (!is_dir("{$termin_location_img_path}img/")) {
            mkdir("{$termin_location_img_path}img/", 0777, true);
        }
        if (!is_dir("{$termin_location_img_path}thumb/")) {
            mkdir("{$termin_location_img_path}thumb/", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($valid_image_ids, "{$termin_location_img_path}img/");
        // TODO: Generate default thumbnails.
    }
}
