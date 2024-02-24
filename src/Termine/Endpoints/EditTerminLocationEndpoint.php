<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditTerminLocationEndpoint extends OlzEditEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'EditTerminLocationEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_location = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_location, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_ids = $termin_location->getImageIds();
        $termin_location_img_path = "{$data_path}img/termin_locations/{$termin_location->getId()}/";
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
