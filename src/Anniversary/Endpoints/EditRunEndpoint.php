<?php

namespace Olz\Anniversary\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRunId from RunEndpointTrait
 * @phpstan-import-type OlzRunData from RunEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzRunId, OlzRunData>
 */
class EditRunEndpoint extends OlzEditEntityTypedEndpoint {
    use RunEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'anniversary')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
