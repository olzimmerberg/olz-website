<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditTerminTemplateEndpoint extends OlzEditEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'EditTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_template = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_template, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($termin_template);

        return [
            'id' => $termin_template->getId(),
            'meta' => $termin_template->getMetaData(),
            'data' => $this->getEntityData($termin_template),
        ];
    }
}
