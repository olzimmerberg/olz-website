<?php

namespace Olz\Snippets\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzSnippetId from SnippetEndpointTrait
 * @phpstan-import-type OlzSnippetData from SnippetEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzSnippetId, OlzSnippetData>
 */
class GetSnippetEndpoint extends OlzGetEntityTypedEndpoint {
    use SnippetEndpointTrait;

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
