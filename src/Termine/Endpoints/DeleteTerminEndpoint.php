<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class DeleteTerminEndpoint extends OlzDeleteEntityTypedEndpoint {
    use TerminEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        $current_user = $this->authUtils()->getCurrentUser();
        $organizer_user = $entity->getOrganizerUser();
        $is_organizer = ($organizer_user && $current_user?->getId() === $organizer_user->getId());

        if (
            !$this->entityUtils()->canUpdateOlzEntity($entity, null, 'termine_admin')
            && !$is_organizer
        ) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
