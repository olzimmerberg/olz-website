<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\Termin;

class CreateTerminEndpoint extends OlzCreateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'CreateTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

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
