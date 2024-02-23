<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteTerminLocationEndpoint extends OlzDeleteEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminLocationEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_location = $this->getEntityById($input['id']);

        if (!$termin_location) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_location, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_location->setOnOff(0);
        $this->entityManager()->persist($termin_location);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
