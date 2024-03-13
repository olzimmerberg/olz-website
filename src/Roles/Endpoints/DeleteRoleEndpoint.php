<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteRoleEndpoint extends OlzDeleteEntityEndpoint {
    use RoleEndpointTrait;

    public static function getIdent() {
        return 'DeleteRoleEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('roles');

        $entity = $this->getEntityById($input['id']);

        if (!$entity) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'roles')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity->setOnOff(0);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
