<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzListEntitiesEndpoint;

class ListTerminLabelsEndpoint extends OlzListEntitiesEndpoint {
    use TerminLabelEndpointTrait;

    public static function getIdent() {
        return 'ListTerminLabelsEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $entities = $this->listEntities();

        return [
            'items' => array_map(function ($entity) {
                return [
                    'id' => $entity->getId(),
                    'meta' => $entity->getMetaData(),
                    'data' => $this->getEntityData($entity),
                ];
            }, $entities),
        ];
    }
}
