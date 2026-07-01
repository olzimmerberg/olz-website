<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class UpdateTerminEndpoint extends OlzUpdateEntityTypedEndpoint {
    use TerminEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        $current_user = $this->authUtils()->getCurrentUser();
        $organizer_user = $entity->getOrganizerUser();
        $is_organizer = ($organizer_user && $current_user?->getId() === $organizer_user->getId());

        if (
            !$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'termine_admin')
            && !$is_organizer
        ) {
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
