<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzAddRelationTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRoleMembershipIds from UserRoleMembershipEndpointTrait
 *
 * @extends OlzAddRelationTypedEndpoint<OlzRoleMembershipIds>
 */
class AddUserRoleMembershipEndpoint extends OlzAddRelationTypedEndpoint {
    use UserRoleMembershipEndpointTrait;

    protected function handle(mixed $input): mixed {
        $role = $this->getRoleEntityById($input['ids']['roleId']);
        $user = $this->getUserEntityById($input['ids']['userId']);

        $is_superior = $this->authUtils()->hasRoleEditPermission($input['ids']['roleId']);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($role, null, 'roles');
        if (!$is_superior && !$is_owner) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $role->addUser($user);
        $user->addRole($role);
        $this->entityManager()->persist($role);
        $this->entityManager()->persist($user);
        $this->entityManager()->flush();

        return [];
    }
}
