<?php

namespace Olz\Roles\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait UserRoleMembershipEndpointTrait {
    use WithUtilsTrait;

    public function getIdsField(): FieldTypes\ObjectField {
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'roleId' => new FieldTypes\IntegerField(['min_value' => 1]),
                'userId' => new FieldTypes\IntegerField(['min_value' => 1]),
            ],
        ]);
    }

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
