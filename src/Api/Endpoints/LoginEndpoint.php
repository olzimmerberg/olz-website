<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;
use PhpTypeScriptApi\Fields\FieldTypes;

class LoginEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'LoginEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'INVALID_CREDENTIALS',
                'BLOCKED',
                'AUTHENTICATED',
            ]]),
            'numRemainingAttempts' => new FieldTypes\IntegerField([
                'min_value' => 0,
                'allow_null' => true,
            ]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'usernameOrEmail' => new FieldTypes\StringField([]),
            'password' => new FieldTypes\StringField([]),
            'rememberMe' => new FieldTypes\BooleanField([]),
        ]]);
    }

    protected function handle($input) {
        $username_or_email = trim($input['usernameOrEmail']);
        $password = $input['password'];
        $remember_me = $input['rememberMe'];

        try {
            $user = $this->authUtils()->authenticate($username_or_email, $password);
        } catch (AuthBlockedException $exc) {
            return [
                'status' => 'BLOCKED',
                'numRemainingAttempts' => 0,
            ];
        } catch (InvalidCredentialsException $exc) {
            return [
                'status' => 'INVALID_CREDENTIALS',
                'numRemainingAttempts' => $exc->getNumRemainingAttempts(),
            ];
        }

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $user->setLastLoginAt($now_datetime);
        $this->entityManager()->flush();

        $this->session()->resetConfigure([
            'timeout' => $remember_me ? 2419200 : 3600, // a month / an hour
        ]);

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        $this->session()->set('auth', $user->getPermissions());
        $this->session()->set('root', $root);
        $this->session()->set('user', $user->getUsername());
        $this->session()->set('user_id', $user->getId());
        $this->session()->set('auth_user', $user->getUsername());
        $this->session()->set('auth_user_id', $user->getId());
        return [
            'status' => 'AUTHENTICATED',
            'numRemainingAttempts' => null,
        ];
    }
}
