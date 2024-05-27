<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\ValidationError;
use PhpTypeScriptApi\HttpError;

class UpdateRoleEndpoint extends OlzUpdateEntityEndpoint {
    use RoleEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateRoleEndpoint';
    }

    protected function handle($input) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $entity = $this->getEntityById($input['id']);

        $is_superior = $this->authUtils()->hasRoleEditPermission($input['id']);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($entity, null, 'roles');
        if (!$is_superior && !$is_owner) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        // Username validation
        $new_username = $input['data']['username'];
        $is_username_updated = $new_username !== $entity->getUsername();
        if (!$this->authUtils()->isUsernameAllowed($new_username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        if ($is_username_updated) {
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
        }

        // TODO Do this more elegantly?
        $old_data = $this->getEntityData($entity);
        $this->log()->notice('OLD:', [$old_data]);

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
        if ($is_username_updated) {
            $entity->setOldUsername($entity->getUsername());
        }
        $this->updateEntityWithData($entity, $input['data']);

        // TODO Do this more elegantly?
        $new_data = $this->getEntityData($entity);
        $this->log()->notice('NEW:', [$new_data]);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
