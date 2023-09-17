<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\Termine\TerminLocation;
use PhpTypeScriptApi\HttpError;

class DeleteTerminLocationEndpoint extends OlzDeleteEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminLocationEndpoint';
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
