<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class GetAuthenticatedRolesEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'GetAuthenticatedRolesEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'roles' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\ObjectField([
                    'export_as' => 'OlzAuthenticatedRole',
                    'field_structure' => [
                        'id' => new FieldTypes\IntegerField([]),
                        'name' => new FieldTypes\StringField(['allow_empty' => false]),
                        'username' => new FieldTypes\StringField(['allow_empty' => false]),
                    ],
                ]),
                'allow_null' => true,
            ]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

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
