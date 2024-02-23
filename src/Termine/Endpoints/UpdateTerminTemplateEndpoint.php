<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateTerminTemplateEndpoint extends OlzUpdateEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_template = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_template, $input['meta'], 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($termin_template, $input['meta'] ?? []);
        $this->updateEntityWithData($termin_template, $input['data']);

        $this->entityManager()->persist($termin_template);
        $this->entityManager()->flush();
        $this->persistUploads($termin_template, $input['data']);

        return [
            'status' => 'OK',
            'id' => $termin_template->getId(),
        ];
    }
}
