<?php

namespace Olz\Utils;

use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;

class EntityUtils {
    use WithUtilsTrait;

    public function createOlzEntity(OlzEntity $entity, array $input) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getCurrentUser();
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());

        $on_off = ($input['onOff'] ?? false) ? 1 : 0;

        $owner_user_id = $input['ownerUserId'] ?? null;
        $owner_user = $current_user;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        $owner_role = null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
        }

        $entity->setOnOff($on_off);
        $entity->setOwnerUser($owner_user);
        $entity->setOwnerRole($owner_role);
        $entity->setCreatedAt($now_datetime);
        $entity->setCreatedByUser($current_user);
        $entity->setLastModifiedAt($now_datetime);
        $entity->setLastModifiedByUser($current_user);
    }

    public function updateOlzEntity(OlzEntity $entity, array $input) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getCurrentUser();
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());

        $on_off = ($input['onOff'] ?? null) ? 1 : 0;

        $owner_user_id = $input['ownerUserId'] ?? null;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
            $entity->setOwnerUser($owner_user);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
            $entity->setOwnerRole($owner_role);
        }

        $entity->setOnOff($on_off);
        $entity->setLastModifiedAt($now_datetime);
        $entity->setLastModifiedByUser($current_user);
    }

    public function canUpdateOlzEntity(
        OlzEntity $entity,
        ?array $meta_arg,
        string $edit_permission = 'all',
    ) {
        $meta = $meta_arg ?? [];
        $auth_utils = $this->authUtils();
        $current_user = $auth_utils->getCurrentUser();

        if ($auth_utils->hasPermission($edit_permission)) {
            return true;
        }

        $owner_user = $entity->getOwnerUser();
        if ($owner_user && $current_user->getId() === $owner_user->getId()) {
            return true;
        }

        $created_by_user = $entity->getCreatedByUser();
        if ($created_by_user && $current_user->getId() === $created_by_user->getId()) {
            return true;
        }

        // TODO: Check roles

        return false;
    }
}
