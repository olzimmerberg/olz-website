<?php

require_once __DIR__.'/../../model/index.php';
require_once __DIR__.'/../WithUtilsTrait.php';

class AuthBlockedException extends Exception {
}
class InvalidCredentialsException extends Exception {
}

class AuthUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'dateUtils',
        'entityManager',
        'getParams',
        'logger',
        'server',
        'session',
    ];

    protected $cached_permission_map_by_user = [];

    public function authenticate($username_or_email, $password) {
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);

        // If there are invalid credentials provided too often, we block.
        $can_login = $auth_request_repo->canAuthenticate($ip_address);
        if (!$can_login) {
            $message = "Login attempt from blocked IP: {$ip_address} (user: {$username_or_email}).";
            $this->logger->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'BLOCKED', $username_or_email);
            throw new AuthBlockedException($message);
        }

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $username_or_email]);
        if (!$user) {
            $user = $user_repo->findOneBy(['email' => $username_or_email]);
        }

        // If the password is wrong, authentication fails.
        if (!$user || !$password || !password_verify($password, $user->getPasswordHash())) {
            $message = "Login attempt with invalid credentials from IP: {$ip_address} (user: {$username_or_email}).";
            $this->logger->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_CREDENTIALS', $username_or_email);
            throw new InvalidCredentialsException($message);
        }

        $this->logger->info("User login successful: {$username_or_email}");
        $this->logger->info("  Auth: {$user->getZugriff()}");
        $this->logger->info("  Root: {$user->getRoot()}");
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED', $username_or_email);
        return $user;
    }

    public function validateAccessToken($access_token) {
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);

        // If there are invalid credentials provided too often, we block.
        $can_validate = $auth_request_repo->canValidateAccessToken($ip_address);
        if (!$can_validate) {
            $message = "Access token validation from blocked IP: {$ip_address}.";
            $this->logger->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'TOKEN_BLOCKED', '');
            throw new AuthBlockedException($message);
        }

        $access_token_repo = $this->entityManager->getRepository(AccessToken::class);
        $access_token = $access_token_repo->findOneBy(['token' => $access_token]);
        $user = $access_token ? $access_token->getUser() : null;

        // If the access token is invalid, authentication fails.
        if (!$access_token || !$user) {
            $message = "Invalid access token validation from IP: {$ip_address}.";
            $this->logger->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_TOKEN', '');
            throw new InvalidCredentialsException($message);
        }

        $now = $this->dateUtils->getIsoNow();
        $expires_at = $access_token->getExpiresAt();
        $is_access_token_expired = (
            $expires_at !== null
            && $expires_at->format('Y-m-d H:i:s') < $now
        );

        // If the access token is expired, authentication fails.
        if ($is_access_token_expired) {
            $message = "Expired access token validation from IP: {$ip_address}.";
            $this->logger->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'EXPIRED_TOKEN', '');
            throw new InvalidCredentialsException($message);
        }

        $this->logger->info("Token validation successful: {$access_token->getId()}");
        $auth_request_repo->addAuthRequest($ip_address, 'TOKEN_VALIDATED', $user->getUsername());
        return $user;
    }

    public function hasPermission($query, $user = null) {
        $permission_map = $this->getPermissionMap($user);
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    protected function getPermissionMap($user = null) {
        if (!$user) {
            $user = $this->getAuthenticatedUser();
        }
        if (!$user) {
            return ['any' => false];
        }
        $user_id = $user->getId();
        $permission_map = $this->cached_permission_map_by_user[$user_id] ?? null;
        if ($permission_map != null) {
            return $permission_map;
        }
        $permission_list = preg_split('/[ ]+/', $user->getZugriff());
        $permission_map = ['any' => true];
        foreach ($permission_list as $permission) {
            $permission_map[$permission] = true;
        }
        $this->cached_permission_map_by_user[$user_id] = $permission_map;
        return $permission_map;
    }

    public function getAuthenticatedUser() {
        $user = $this->getTokenUser();
        if ($user) {
            return $user;
        }
        return $this->getSessionUser();
    }

    public function getTokenUser() {
        $access_token = $this->getParams['access_token'] ?? null;
        if (!$access_token) {
            return null;
        }
        try {
            return $this->validateAccessToken($access_token);
        } catch (Exception $exc) {
            return null;
        }
    }

    public function getSessionUser() {
        $auth_username = $this->session->get('user');
        $user_repo = $this->entityManager->getRepository(User::class);
        return $user_repo->findOneBy(['username' => $auth_username]);
    }

    public function isUsernameAllowed($username) {
        return preg_match('/^[a-zA-Z0-9-_\\.]+$/', $username) ? true : false;
    }

    public function isPasswordAllowed($password) {
        return strlen($password) >= 8;
    }
}
