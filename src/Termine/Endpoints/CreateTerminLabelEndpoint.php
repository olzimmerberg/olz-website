<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Termine\TerminLabel;

/**
 * @phpstan-import-type OlzTerminLabelId from TerminLabelEndpointTrait
 * @phpstan-import-type OlzTerminLabelData from TerminLabelEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzTerminLabelId, OlzTerminLabelData>
 */
class CreateTerminLabelEndpoint extends OlzCreateEntityTypedEndpoint {
    use TerminLabelEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine_admin');

        $entity = new TerminLabel();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
