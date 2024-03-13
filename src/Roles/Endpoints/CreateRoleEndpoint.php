<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\ValidationError;

class CreateRoleEndpoint extends OlzCreateEntityEndpoint {
    use RoleEndpointTrait;

    public static function getIdent() {
        return 'CreateRoleEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('roles');

        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);

        // Username validation
        $new_username = $input['data']['username'];
        if (!$this->authUtils()->isUsernameAllowed($new_username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        $same_username_user = $user_repo->findOneBy(['username' => $new_username]);
        $same_old_username_user = $user_repo->findOneBy(['old_username' => $new_username]);
        $same_username_role = $role_repo->findOneBy(['username' => $new_username]);
        $same_old_username_role = $role_repo->findOneBy(['old_username' => $new_username]);
        $is_existing_username = (bool) (
            $same_username_user || $same_old_username_user
            || $same_username_role || $same_old_username_role
        );
        if ($is_existing_username) {
            throw new ValidationError(['username' => ["Dieser Benutzername ist bereits vergeben."]]);
        }

        $entity = new Role();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
