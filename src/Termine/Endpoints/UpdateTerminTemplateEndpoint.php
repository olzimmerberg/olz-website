<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminTemplateId from TerminTemplateEndpointTrait
 * @phpstan-import-type OlzTerminTemplateData from TerminTemplateEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzTerminTemplateId, OlzTerminTemplateData>
 */
class UpdateTerminTemplateEndpoint extends OlzUpdateEntityTypedEndpoint {
    use TerminTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
