<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzRemoveRelationEndpoint;
use PhpTypeScriptApi\HttpError;

class RemoveUserRoleMembershipEndpoint extends OlzRemoveRelationEndpoint {
    use UserRoleMembershipEndpointTrait;

    public static function getIdent() {
        return 'RemoveUserRoleMembershipEndpoint';
    }

    protected function handle($input) {
        $role = $this->getRoleEntityById($input['ids']['roleId']);
        $user = $this->getUserEntityById($input['ids']['userId']);

        $is_superior = $this->authUtils()->hasRoleEditPermission($input['ids']['roleId']);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($role, null, 'roles');
        if (!$is_superior && !$is_owner) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $role->removeUser($user);
        $this->entityManager()->persist($role);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
