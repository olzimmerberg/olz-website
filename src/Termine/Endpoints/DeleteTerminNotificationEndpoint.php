<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminNotificationId from TerminNotificationEndpointTrait
 * @phpstan-import-type OlzTerminNotificationData from TerminNotificationEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzTerminNotificationId, OlzTerminNotificationData>
 */
class DeleteTerminNotificationEndpoint extends OlzDeleteEntityTypedEndpoint {
    use TerminNotificationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity->getTermin(), null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityManager()->remove($entity);
        $this->entityManager()->flush();

        return [];
    }
}
