<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\TerminLocation;
use PhpTypeScriptApi\HttpError;

class CreateTerminLocationEndpoint extends OlzCreateEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'CreateTerminLocationEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_location = new TerminLocation();
        $this->entityUtils()->createOlzEntity($termin_location, $input['meta']);
        $this->updateEntityWithData($termin_location, $input['data']);

        $this->entityManager()->persist($termin_location);
        $this->entityManager()->flush();
        $this->persistUploads($termin_location);

        return [
            'status' => 'OK',
            'id' => $termin_location->getId(),
        ];
    }
}
