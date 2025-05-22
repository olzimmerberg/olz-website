<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Utils\AuthUtilsTrait;

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
    use AuthUtilsTrait;

    protected function handle(mixed $input): mixed {
        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            return ['user' => null];
        }
        return [
            'user' => [
                'id' => $user->getId() ?? 0,
                'firstName' => $user->getFirstName() ?: '-',
                'lastName' => $user->getLastName() ?: '-',
                'username' => $user->getUsername() ?: '-',
            ],
        ];
    }
}
