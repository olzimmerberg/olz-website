<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetRoleEndpoint extends OlzGetEntityEndpoint {
    use RoleEndpointTrait;

    public static function getIdent(): string {
        return 'GetRoleEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
