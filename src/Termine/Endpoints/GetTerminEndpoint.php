<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\HttpError;

class GetTerminEndpoint extends OlzGetEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'GetTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $termin->getId(),
            'meta' => $termin->getMetaData(),
            'data' => $this->getEntityData($termin),
        ];
    }
}
