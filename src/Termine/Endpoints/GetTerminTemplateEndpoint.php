<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetTerminTemplateEndpoint extends OlzGetEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'GetTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $termin_template = $this->getEntityById($input['id']);

        return [
            'id' => $termin_template->getId(),
            'meta' => $termin_template->getMetaData(),
            'data' => $this->getEntityData($termin_template),
        ];
    }
}
