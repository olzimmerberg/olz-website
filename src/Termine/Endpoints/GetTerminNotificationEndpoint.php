<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminNotificationId from TerminNotificationEndpointTrait
 * @phpstan-import-type OlzTerminNotificationData from TerminNotificationEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzTerminNotificationId, OlzTerminNotificationData>
 */
class GetTerminNotificationEndpoint extends OlzGetEntityTypedEndpoint {
    use TerminNotificationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getTermin()->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
