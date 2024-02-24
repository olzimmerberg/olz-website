<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetTerminEndpoint extends OlzGetEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'GetTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $termin = $this->getEntityById($input['id']);

        return [
            'id' => $termin->getId(),
            'meta' => $termin->getMetaData(),
            'data' => $this->getEntityData($termin),
        ];
    }
}
