<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/AuthRequest.php';
require_once __DIR__.'/../../model/User.php';

class LoginEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'LoginEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'INVALID_CREDENTIALS',
                'BLOCKED',
                'AUTHENTICATED',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            new StringField('username', ['max_length' => 20]),
            new StringField('password', []),
        ];
    }

    protected function handle($input) {
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);
        $username = trim($input['username']);

        // If there are invalid credentials provided too often, we block.
        $can_login = $auth_request_repo->canAuthenticate($ip_address);
        if (!$can_login) {
            $auth_request_repo->addAuthRequest($ip_address, 'BLOCKED', $username);
            return [
                'status' => 'BLOCKED',
            ];
        }

        $password = $input['password'];
        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $username]);
        if (!$user || !password_verify($password, $user->getPasswordHash())) {
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_CREDENTIALS', $username);
            return [
                'status' => 'INVALID_CREDENTIALS',
            ];
        }
        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        // Mögliche Werte für 'zugriff': all, ftp, termine, mail
        $this->session->set('auth', $user->getZugriff());
        $this->session->set('root', $root);
        $this->session->set('user', $username);
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED', $username);
        return [
            'status' => 'AUTHENTICATED',
        ];
    }
}
