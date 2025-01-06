<?php

namespace Olz\Roles\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzRoleMembershipIds array{
 *   roleId: int<1, max>,
 *   userId: int<1, max>,
 * }
 */
trait UserRoleMembershipEndpointTrait {
    use WithUtilsTrait;

    protected function getRoleEntityById(int $id): Role {
        $repo = $this->entityManager()->getRepository(Role::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }

    protected function getUserEntityById(int $id): User {
        $repo = $this->entityManager()->getRepository(User::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
