<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzListEntitiesTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminLabelId from TerminLabelEndpointTrait
 * @phpstan-import-type OlzTerminLabelData from TerminLabelEndpointTrait
 *
 * @extends OlzListEntitiesTypedEndpoint<OlzTerminLabelId, OlzTerminLabelData>
 */
class ListTerminLabelsEndpoint extends OlzListEntitiesTypedEndpoint {
    use TerminLabelEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entities = $this->listEntities();

        return [
            'items' => array_map(function ($entity): array {
                return [
                    'id' => $entity->getId() ?? 0,
                    'meta' => $entity->getMetaData(),
                    'data' => $this->getEntityData($entity),
                ];
            }, $entities),
        ];
    }
}
