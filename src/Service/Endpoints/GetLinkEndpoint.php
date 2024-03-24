<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetLinkEndpoint extends OlzGetEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'GetLinkEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
