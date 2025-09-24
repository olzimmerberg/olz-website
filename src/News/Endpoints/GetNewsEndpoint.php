<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzNewsId from NewsEndpointTrait
 * @phpstan-import-type OlzNewsData from NewsEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzNewsFormat from NewsEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzNewsId, OlzNewsData>
 */
class GetNewsEndpoint extends OlzGetEntityTypedEndpoint {
    use NewsEndpointTrait;

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
