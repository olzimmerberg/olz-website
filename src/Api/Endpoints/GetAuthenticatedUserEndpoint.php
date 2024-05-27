<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class GetAuthenticatedUserEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'GetAuthenticatedUserEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'user' => new FieldTypes\ObjectField([
                'export_as' => 'OlzAuthenticatedUser',
                'field_structure' => [
                    'id' => new FieldTypes\IntegerField([]),
                    'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
                    'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
                    'username' => new FieldTypes\StringField(['allow_empty' => false]),
                ],
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
        $user = $auth_utils->getCurrentUser();
        if (!$user) {
            return ['user' => null];
        }
        return [
            'user' => [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'username' => $user->getUsername(),
            ],
        ];
    }
}
