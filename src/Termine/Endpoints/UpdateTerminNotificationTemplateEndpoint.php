<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminNotificationTemplateId from TerminNotificationTemplateEndpointTrait
 * @phpstan-import-type OlzTerminNotificationTemplateData from TerminNotificationTemplateEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzTerminNotificationTemplateId, OlzTerminNotificationTemplateData>
 */
class UpdateTerminNotificationTemplateEndpoint extends OlzUpdateEntityTypedEndpoint {
    use TerminNotificationTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity->getTerminTemplate(), null, 'termine_admin')) {
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
