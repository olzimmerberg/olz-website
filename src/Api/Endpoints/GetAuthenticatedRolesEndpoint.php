<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Utils\AuthUtilsTrait;

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
    use AuthUtilsTrait;

    protected function handle(mixed $input): mixed {
        $roles = $this->authUtils()->getAuthenticatedRoles();
        if ($roles === null) {
            return ['roles' => null];
        }
        return [
            'roles' => array_map(function ($role) {
                return [
                    'id' => $role->getId() ?? 0,
                    'name' => $role->getName() ?: '-',
                    'username' => $role->getUsername() ?: '-',
                ];
            }, $roles),
        ];
    }
}
