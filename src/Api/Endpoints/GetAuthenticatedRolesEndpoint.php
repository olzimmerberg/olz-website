<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;

/**
 * @phpstan-type OlzAuthenticatedRole array{
 *   id: int,
 *   name: non-empty-string,
 *   username: non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     roles?: ?array<OlzAuthenticatedRole>
 *   }
 * >
 */
class GetAuthenticatedRolesEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $auth_utils = $this->authUtils();
        $roles = $auth_utils->getAuthenticatedRoles();
        if ($roles === null) {
            return ['roles' => null];
        }
        return [
            'roles' => array_map(function ($role) {
                return [
                    'id' => $role->getId(),
                    'name' => $role->getName(),
                    'username' => $role->getUsername(),
                ];
            }, $roles),
        ];
    }
}
