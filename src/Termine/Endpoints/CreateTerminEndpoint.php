<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\HttpError;

class CreateTerminEndpoint extends OlzCreateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'CreateTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin = new Termin();
        $this->entityUtils()->createOlzEntity($termin, $input['meta']);
        $this->updateEntityWithData($termin, $input['data']);

        $this->entityManager()->persist($termin);
        $this->entityManager()->flush();
        $this->persistUploads($termin, $input['data']);

        return [
            'status' => 'OK',
            'id' => $termin->getId(),
        ];
    }
}
