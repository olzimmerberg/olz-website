<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteTerminTemplateEndpoint extends OlzDeleteEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_template = $this->getEntityById($input['id']);

        if (!$termin_template) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_template, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_template->setOnOff(0);
        $this->entityManager()->persist($termin_template);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
