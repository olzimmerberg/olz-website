<?php

namespace Olz\Anniversary\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRunId from RunEndpointTrait
 * @phpstan-import-type OlzRunData from RunEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzRunId, OlzRunData>
 */
class DeleteRunEndpoint extends OlzDeleteEntityTypedEndpoint {
    use RunEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'anniversary')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
