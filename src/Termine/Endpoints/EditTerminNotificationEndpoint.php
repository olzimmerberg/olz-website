<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminNotificationId from TerminNotificationEndpointTrait
 * @phpstan-import-type OlzTerminNotificationData from TerminNotificationEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminNotificationId, OlzTerminNotificationData>
 */
class EditTerminNotificationEndpoint extends OlzEditEntityTypedEndpoint {
    use TerminNotificationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity->getTermin(), null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getTermin()->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
