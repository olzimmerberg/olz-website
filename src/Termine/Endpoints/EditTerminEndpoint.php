<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditTerminEndpoint extends OlzEditEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'EditTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($termin);

        return [
            'id' => $termin->getId(),
            'meta' => $termin->getMetaData(),
            'data' => $this->getEntityData($termin),
        ];
    }
}
