<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzRoleId from RoleEndpointTrait
 * @phpstan-import-type OlzRoleData from RoleEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzRoleId, OlzRoleData>
 */
class GetRoleEndpoint extends OlzGetEntityTypedEndpoint {
    use RoleEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
