<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Termine\TerminLocation;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class EditTerminLocationEndpoint extends OlzEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'EditTerminLocationEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $entity_id]);

        if (!$termin_location) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($termin_location, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_ids = $termin_location->getImageIds();
        $termin_location_img_path = "{$data_path}img/termin_locations/{$entity_id}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$termin_location_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        return [
            'id' => $termin_location->getId(),
            'meta' => $termin_location->getMetaData(),
            'data' => $this->getEntityData($termin_location),
        ];
    }
}
