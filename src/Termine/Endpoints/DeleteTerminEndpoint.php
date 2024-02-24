<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteTerminEndpoint extends OlzDeleteEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($termin, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin->setOnOff(0);
        $this->entityManager()->persist($termin);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
