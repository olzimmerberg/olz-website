<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminNotificationTemplateId from TerminNotificationTemplateEndpointTrait
 * @phpstan-import-type OlzTerminNotificationTemplateData from TerminNotificationTemplateEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzTerminNotificationTemplateId, OlzTerminNotificationTemplateData>
 */
class DeleteTerminNotificationTemplateEndpoint extends OlzDeleteEntityTypedEndpoint {
    use TerminNotificationTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity->getTerminTemplate(), null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityManager()->remove($entity);
        $this->entityManager()->flush();

        return [];
    }
}
