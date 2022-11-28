<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class GetAuthenticatedUserEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetAuthenticatedUserEndpoint';
    }

    public function getResponseField() {
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

    public function getRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    protected function handle($input) {
        $auth_utils = $this->authUtils();
        $user = $auth_utils->getAuthenticatedUser();
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
