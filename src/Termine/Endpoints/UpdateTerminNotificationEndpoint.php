<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminNotificationId from TerminNotificationEndpointTrait
 * @phpstan-import-type OlzTerminNotificationData from TerminNotificationEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzTerminNotificationId, OlzTerminNotificationData>
 */
class UpdateTerminNotificationEndpoint extends OlzUpdateEntityTypedEndpoint {
    use TerminNotificationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity->getTermin(), null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
