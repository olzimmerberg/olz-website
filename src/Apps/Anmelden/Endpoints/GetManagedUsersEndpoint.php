<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @phpstan-type ManagedUser array{
 *   id: int,
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 * }
 *
 * @extends TypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'OK'|'ERROR',
 *     managedUsers: ?array<ManagedUser>
 *   },
 * >
 */
class GetManagedUsersEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [];
    }

    public static function getIdent(): string {
        return 'GetManagedUsersEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $auth_user = $this->authUtils()->getCurrentUser();
        $auth_user_id = $auth_user->getId();
        $user_repo = $this->entityManager()->getRepository(User::class);
        $users = $user_repo->findBy(['parent_user' => $auth_user_id]);
        $managed_users = [];
        foreach ($users as $user) {
            $managed_users[] = [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ];
        }

        return [
            'status' => 'OK',
            'managedUsers' => $managed_users,
        ];
    }
}
