<?php

namespace Olz\Anniversary\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzRunId from RunEndpointTrait
 * @phpstan-import-type OlzRunData from RunEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzRunId, OlzRunData>
 */
class GetRunEndpoint extends OlzGetEntityTypedEndpoint {
    use RunEndpointTrait;

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
