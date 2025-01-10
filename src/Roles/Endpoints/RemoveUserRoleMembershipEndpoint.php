<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzRemoveRelationTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRoleMembershipIds from UserRoleMembershipEndpointTrait
 *
 * @extends OlzRemoveRelationTypedEndpoint<OlzRoleMembershipIds>
 */
class RemoveUserRoleMembershipEndpoint extends OlzRemoveRelationTypedEndpoint {
    use UserRoleMembershipEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(UserRoleMembershipEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $role = $this->getRoleEntityById($input['ids']['roleId']);
        $user = $this->getUserEntityById($input['ids']['userId']);

        $is_superior = $this->authUtils()->hasRoleEditPermission($input['ids']['roleId']);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($role, null, 'roles');
        if (!$is_superior && !$is_owner) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $role->removeUser($user);
        $user->removeRole($role);
        $this->entityManager()->persist($role);
        $this->entityManager()->persist($user);
        $this->entityManager()->flush();

        return [];
    }
}
