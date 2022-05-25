<?php

use Olz\Utils\AuthBlockedException;
use Olz\Utils\InvalidCredentialsException;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class LoginEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'LoginEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'INVALID_CREDENTIALS',
                'BLOCKED',
                'AUTHENTICATED',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'usernameOrEmail' => new FieldTypes\StringField([]),
            'password' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle($input) {
        $username_or_email = trim($input['usernameOrEmail']);
        $password = $input['password'];

        try {
            $user = $this->authUtils->authenticate($username_or_email, $password);
        } catch (AuthBlockedException $exc) {
            return [
                'status' => 'BLOCKED',
            ];
        } catch (InvalidCredentialsException $exc) {
            return [
                'status' => 'INVALID_CREDENTIALS',
            ];
        }

        $now_datetime = new \DateTime($this->dateUtils->getIsoNow());
        $user->setLastLoginAt($now_datetime);
        $this->entityManager->flush();

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        // Mögliche Werte für 'zugriff': all, ftp, termine, mail
        $this->session->set('auth', $user->getZugriff());
        $this->session->set('root', $root);
        $this->session->set('user', $user->getUsername());
        $this->session->set('user_id', $user->getId());
        return [
            'status' => 'AUTHENTICATED',
        ];
    }
}
