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
            new StringField('usernameOrEmail', []),
            new StringField('password', []),
        ];
    }

    protected function handle($input) {
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);
        $username_or_email = trim($input['usernameOrEmail']);

        // If there are invalid credentials provided too often, we block.
        $can_login = $auth_request_repo->canAuthenticate($ip_address);
        if (!$can_login) {
            $this->logger->notice("Login attempt from blocked user: {$username_or_email} ({$ip_address}).");
            $auth_request_repo->addAuthRequest($ip_address, 'BLOCKED', $username_or_email);
            return [
                'status' => 'BLOCKED',
            ];
        }

        $password = $input['password'];
        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $username_or_email]);
        if (!$user) {
            $user = $user_repo->findOneBy(['email' => $username_or_email]);
        }
        if (!$user || !password_verify($password, $user->getPasswordHash())) {
            $this->logger->notice("Login attempt with invalid credentials from user: {$username_or_email} ({$ip_address}).");
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_CREDENTIALS', $username_or_email);
            return [
                'status' => 'INVALID_CREDENTIALS',
            ];
        }
        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        // Mögliche Werte für 'zugriff': all, ftp, termine, mail
        $this->session->set('auth', $user->getZugriff());
        $this->session->set('root', $root);
        $this->session->set('user', $user->getUsername());
        $this->session->set('user_id', $user->getId());
        $this->logger->info("User logged in: {$username_or_email}");
        $this->logger->info("  Auth: {$user->getZugriff()}");
        $this->logger->info("  Root: {$root}");
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED', $username_or_email);
        return [
            'status' => 'AUTHENTICATED',
        ];
    }
}
