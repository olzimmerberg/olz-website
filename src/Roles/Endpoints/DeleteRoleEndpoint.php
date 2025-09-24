<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRoleId from RoleEndpointTrait
 * @phpstan-import-type OlzRoleData from RoleEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzRoleId, OlzRoleData>
 */
class DeleteRoleEndpoint extends OlzDeleteEntityTypedEndpoint {
    use RoleEndpointTrait;

    protected function handle(mixed $input): mixed {
        $entity = $this->getEntityById($input['id']);

        $is_superior = $this->authUtils()->hasRoleEditPermission($input['id']);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($entity, null, 'roles');
        if (!$is_superior && !$is_owner) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
