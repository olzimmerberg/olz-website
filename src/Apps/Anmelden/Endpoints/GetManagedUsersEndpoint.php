<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @extends TypedEndpoint<
 *   ?array{},
 *   array{status: 'OK'|'ERROR', managedUsers: ?array<array{id: int, firstName: string, lastName: string}>},
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

    // public function getResponseField(): FieldTypes\Field {
    //     return new FieldTypes\ObjectField(['field_structure' => [
    //         'status' => new FieldTypes\EnumField(['allowed_values' => [
    //             'OK',
    //             'ERROR',
    //         ]]),
    //         'managedUsers' => new FieldTypes\ArrayField([
    //             'item_field' => new FieldTypes\ObjectField([
    //                 'field_structure' => [
    //                     'id' => new FieldTypes\IntegerField([]),
    //                     'firstName' => new FieldTypes\StringField([]),
    //                     'lastName' => new FieldTypes\StringField([]),
    //                 ],
    //             ]),
    //             'allow_null' => true,
    //         ]),
    //     ]]);
    // }

    // public function getRequestField(): FieldTypes\Field {
    //     return new FieldTypes\ObjectField([
    //         'field_structure' => [],
    //         'allow_null' => true,
    //     ]);
    // }

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
