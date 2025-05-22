<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\EntityManagerTrait;

/**
 * @phpstan-type ManagedUser array{
 *   id: int,
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'OK'|'ERROR',
 *     managedUsers: ?array<ManagedUser>
 *   },
 * >
 */
class GetManagedUsersEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use EntityManagerTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $auth_user = $this->authUtils()->getCurrentUser();
        $auth_user_id = $auth_user?->getId();
        $user_repo = $this->entityManager()->getRepository(User::class);
        $users = $user_repo->findBy(['parent_user' => $auth_user_id]);
        $managed_users = [];
        foreach ($users as $user) {
            $managed_users[] = [
                'id' => $user->getId() ?? 0,
                'firstName' => $user->getFirstName() ?: '-',
                'lastName' => $user->getLastName() ?: '-',
            ];
        }

        return [
            'status' => 'OK',
            'managedUsers' => $managed_users,
        ];
    }
}
