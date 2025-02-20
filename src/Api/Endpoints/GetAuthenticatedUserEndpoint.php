<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;

/**
 * @phpstan-type OlzAuthenticatedUser array{
 *   id: int,
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 *   username: non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     user?: ?OlzAuthenticatedUser,
 *   }
 * >
 */
class GetAuthenticatedUserEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $auth_utils = $this->authUtils();
        $user = $auth_utils->getCurrentUser();
        if (!$user) {
            return ['user' => null];
        }
        return [
            'user' => [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName() ?: '-',
                'lastName' => $user->getLastName() ?: '-',
                'username' => $user->getUsername() ?: '-',
            ],
        ];
    }
}
