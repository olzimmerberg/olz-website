<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class EditTerminEndpoint extends OlzEditEntityTypedEndpoint {
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

        $this->editUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
