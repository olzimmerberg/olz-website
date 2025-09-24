<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminTemplateId from TerminTemplateEndpointTrait
 * @phpstan-import-type OlzTerminTemplateData from TerminTemplateEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzTerminTemplateId, OlzTerminTemplateData>
 */
class GetTerminTemplateEndpoint extends OlzGetEntityTypedEndpoint {
    use TerminTemplateEndpointTrait;

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
