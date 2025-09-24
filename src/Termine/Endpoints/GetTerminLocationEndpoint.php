<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminLocationId from TerminLocationEndpointTrait
 * @phpstan-import-type OlzTerminLocationData from TerminLocationEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzTerminLocationId, OlzTerminLocationData>
 */
class GetTerminLocationEndpoint extends OlzGetEntityTypedEndpoint {
    use TerminLocationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
