<?php

namespace Olz\Utils;

use Olz\Entity\OlzEntity;
use Olz\Entity\Role;
use Olz\Entity\User;

class EntityUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'authUtils',
        'dateUtils',
        'entityManager',
    ];

    public function createOlzEntity(OlzEntity $entity, $input) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getSessionUser();
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

    public function updateOlzEntity(OlzEntity $entity, $input) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getSessionUser();
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
}
