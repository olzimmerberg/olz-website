<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminNotificationTemplateId from TerminNotificationTemplateEndpointTrait
 * @phpstan-import-type OlzTerminNotificationTemplateData from TerminNotificationTemplateEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminNotificationTemplateId, OlzTerminNotificationTemplateData>
 */
class EditTerminNotificationTemplateEndpoint extends OlzEditEntityTypedEndpoint {
    use TerminNotificationTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity->getTerminTemplate(), null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getTerminTemplate()->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
