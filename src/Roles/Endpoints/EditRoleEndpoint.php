<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditRoleEndpoint extends OlzEditEntityEndpoint {
    use RoleEndpointTrait;

    public static function getIdent() {
        return 'EditRoleEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('roles');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'roles')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($entity);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
