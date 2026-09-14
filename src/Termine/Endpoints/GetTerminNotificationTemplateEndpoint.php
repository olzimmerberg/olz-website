<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminNotificationTemplateId from TerminNotificationTemplateEndpointTrait
 * @phpstan-import-type OlzTerminNotificationTemplateData from TerminNotificationTemplateEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzTerminNotificationTemplateId, OlzTerminNotificationTemplateData>
 */
class GetTerminNotificationTemplateEndpoint extends OlzGetEntityTypedEndpoint {
    use TerminNotificationTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getTerminTemplate()->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
