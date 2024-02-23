<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetTerminLocationEndpoint extends OlzGetEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'GetTerminLocationEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $termin_location = $this->getEntityById($input['id']);

        return [
            'id' => $termin_location->getId(),
            'meta' => $termin_location->getMetaData(),
            'data' => $this->getEntityData($termin_location),
        ];
    }
}
