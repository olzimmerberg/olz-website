<?php

namespace Olz\Anniversary\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Anniversary\RunRecord;

/**
 * @phpstan-import-type OlzRunId from RunEndpointTrait
 * @phpstan-import-type OlzRunData from RunEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzRunId, OlzRunData>
 */
class CreateRunEndpoint extends OlzCreateEntityTypedEndpoint {
    use RunEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = new RunRecord();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
