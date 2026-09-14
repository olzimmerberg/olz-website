<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Termine\TerminNotification;

/**
 * @phpstan-import-type OlzTerminNotificationId from TerminNotificationEndpointTrait
 * @phpstan-import-type OlzTerminNotificationData from TerminNotificationEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzTerminNotificationId, OlzTerminNotificationData>
 */
class CreateTerminNotificationEndpoint extends OlzCreateEntityTypedEndpoint {
    use TerminNotificationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new TerminNotification();
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
