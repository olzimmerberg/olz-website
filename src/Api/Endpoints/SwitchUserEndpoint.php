<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class SwitchUserEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'SwitchUserEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
            ]]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'userId' => new FieldTypes\IntegerField(['min_value' => 1]),
        ]]);
    }

    protected function handle($input) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['userId']]);
        if (!$user) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $auth_user_id = $this->session()->get('auth_user_id');
        $is_parent = $user->getParentUserId() === $auth_user_id;
        $is_self = $user->getId() === $auth_user_id;
        if (!$is_self && !$is_parent) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        $this->session()->set('auth', $user->getPermissions());
        $this->session()->set('root', $root);
        $this->session()->set('user', $user->getUsername());
        $this->session()->set('user_id', $user->getId());
        return [
            'status' => 'OK',
        ];
    }
}
