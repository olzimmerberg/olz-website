<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Termine\TerminLocation;
use PhpTypeScriptApi\HttpError;

class GetTerminLocationEndpoint extends OlzGetEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'GetTerminLocationEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $termin_location->getId(),
            'meta' => $termin_location->getMetaData(),
            'data' => $this->getEntityData($termin_location),
        ];
    }
}
