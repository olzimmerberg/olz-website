<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Termine\TerminNotificationTemplate;

/**
 * @phpstan-import-type OlzTerminNotificationTemplateId from TerminNotificationTemplateEndpointTrait
 * @phpstan-import-type OlzTerminNotificationTemplateData from TerminNotificationTemplateEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzTerminNotificationTemplateId, OlzTerminNotificationTemplateData>
 */
class CreateTerminNotificationTemplateEndpoint extends OlzCreateEntityTypedEndpoint {
    use TerminNotificationTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new TerminNotificationTemplate();
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
