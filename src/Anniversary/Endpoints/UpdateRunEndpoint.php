<?php

namespace Olz\Anniversary\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRunId from RunEndpointTrait
 * @phpstan-import-type OlzRunData from RunEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzRunId, OlzRunData>
 */
class UpdateRunEndpoint extends OlzUpdateEntityTypedEndpoint {
    use RunEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'anniversary')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
