<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;
use PhpTypeScriptApi\Fields\FieldTypes;

class ReauthEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'ReauthEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'INVALID_CREDENTIALS',
                'BLOCKED',
                'AUTHENTICATED',
            ]]),
            'reauthToken' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'usernameOrEmail' => new FieldTypes\StringField([]),
            'reauthToken' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle($input) {
        $username_or_email = trim($input['usernameOrEmail']);
        $reauth_token = $input['reauthToken'];

        try {
            $user = $this->authUtils()->validateReauthAccessToken(
                $reauth_token, $username_or_email);
        } catch (AuthBlockedException $exc) {
            return [
                'status' => 'BLOCKED',
                'reauthToken' => null,
            ];
        } catch (InvalidCredentialsException $exc) {
            return [
                'status' => 'INVALID_CREDENTIALS',
                'reauthToken' => null,
            ];
        }

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $user->setLastLoginAt($now_datetime);
        $this->entityManager()->flush();

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        $this->session()->set('auth', $user->getPermissions());
        $this->session()->set('root', $root);
        $this->session()->set('user', $user->getUsername());
        $this->session()->set('user_id', $user->getId());
        return [
            'status' => 'AUTHENTICATED',
            'reauthToken' => $this->authUtils()->replaceReauthAccessToken(),
        ];
    }
}
