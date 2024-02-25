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

        $this->editUploads($termin_location);

        return [
            'id' => $termin_location->getId(),
            'meta' => $termin_location->getMetaData(),
            'data' => $this->getEntityData($termin_location),
        ];
    }
}
