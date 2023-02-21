<?php

namespace Olz\Utils;

use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Entity\User;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;

class AuthUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'dateUtils',
        'entityManager',
        'getParams',
        'log',
        'server',
        'session',
    ];

    protected $cached_permission_map_by_user = [];
    protected $cached_permission_map_by_role = [];

    public function authenticate($username_or_email, $password) {
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);

        // If there are invalid credentials provided too often, we block.
        $can_login = $auth_request_repo->canAuthenticate($ip_address);
        if (!$can_login) {
            $message = "Login attempt from blocked IP: {$ip_address} (user: {$username_or_email}).";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'BLOCKED', $username_or_email);
            throw new AuthBlockedException($message);
        }

        $user = $this->resolveUsernameOrEmail($username_or_email);

        // If the password is wrong, authentication fails.
        if (!$user || !$password || !password_verify($password, $user->getPasswordHash())) {
            $message = "Login attempt with invalid credentials from IP: {$ip_address} (user: {$username_or_email}).";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_CREDENTIALS', $username_or_email);
            throw new InvalidCredentialsException($message);
        }

        $this->log()->info("User login successful: {$username_or_email}");
        $this->log()->info("  Auth: {$user->getPermissions()}");
        $this->log()->info("  Root: {$user->getRoot()}");
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED', $username_or_email);
        return $user;
    }

    public function validateAccessToken($access_token) {
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);

        // If there are invalid credentials provided too often, we block.
        $can_validate = $auth_request_repo->canValidateAccessToken($ip_address);
        if (!$can_validate) {
            $message = "Access token validation from blocked IP: {$ip_address}.";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'TOKEN_BLOCKED', '');
            throw new AuthBlockedException($message);
        }

        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_token = $access_token_repo->findOneBy(['token' => $access_token]);
        $user = $access_token ? $access_token->getUser() : null;

        // If the access token is invalid, authentication fails.
        if (!$access_token || !$user) {
            $message = "Invalid access token validation from IP: {$ip_address}.";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_TOKEN', '');
            throw new InvalidCredentialsException($message);
        }

        $now = $this->dateUtils()->getIsoNow();
        $expires_at = $access_token->getExpiresAt();
        $is_access_token_expired = (
            $expires_at !== null
            && $expires_at->format('Y-m-d H:i:s') < $now
        );

        // If the access token is expired, authentication fails.
        if ($is_access_token_expired) {
            $message = "Expired access token validation from IP: {$ip_address}.";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'EXPIRED_TOKEN', '');
            throw new InvalidCredentialsException($message);
        }

        $this->log()->info("Token validation successful: {$access_token->getId()}");
        $auth_request_repo->addAuthRequest($ip_address, 'TOKEN_VALIDATED', $user->getUsername());
        return $user;
    }

    public function validateReauthAccessToken($reauth_token, $username_or_email) {
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);

        $user = $this->validateAccessToken($reauth_token);
        $resolved_user = $this->resolveUsernameOrEmail($username_or_email);
        if (!$user || !$resolved_user || $resolved_user->getId() != $user->getId()) {
            $message = "Invalid access token validation from IP: {$ip_address}.";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_TOKEN', '');
            throw new InvalidCredentialsException($message);
        }
        return $user;
    }

    public function replaceReauthAccessToken() {
        $session_user = $this->getSessionUser();
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $token = $this->generateRandomToken(24);

        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $existing_tokens = $access_token_repo->findBy([
            'user' => $session_user,
            'purpose' => 'reauth',
        ]);
        foreach ($existing_tokens as $existing_token) {
            $this->entityManager()->remove($existing_token);
        }

        $access_token = new AccessToken();
        $access_token->setUser($session_user);
        $access_token->setPurpose('reauth');
        $access_token->setToken($token);
        $access_token->setCreatedAt($now);
        $access_token->setExpiresAt(null);

        $this->entityManager()->persist($access_token);
        $this->entityManager()->flush();

        return $token;
    }

    public function resolveUsernameOrEmail($username_or_email) {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $username_or_email]);
        if (!$user) {
            $user = $user_repo->findOneBy(['email' => $username_or_email]);
        }
        if (!$user) {
            $user = $user_repo->findOneBy(['old_username' => $username_or_email]);
        }
        $res = preg_match('/^([a-zA-Z0-9-_\\.]+)@olzimmerberg.ch$/',
            $username_or_email, $matches);
        if (!$user && $res) {
            $user = $user_repo->findOneBy(['username' => $matches[1]]);
        }
        if (!$user && $res) {
            $user = $user_repo->findOneBy(['old_username' => $matches[1]]);
        }
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
        $permission_list = preg_split('/[ ]+/', $user->getPermissions());
        $permission_map = ['any' => true];
        foreach ($permission_list as $permission) {
            $permission_map[$permission] = true;
        }
        $this->cached_permission_map_by_user[$user_id] = $permission_map;
        return $permission_map;
    }

    public function hasRolePermission($query, $role) {
        $permission_map = $this->getRolePermissionMap($role);
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    protected function getRolePermissionMap($role) {
        if (!$role) {
            return ['any' => false];
        }
        $role_id = $role->getId();
        $permission_map = $this->cached_permission_map_by_role[$role_id] ?? null;
        if ($permission_map != null) {
            return $permission_map;
        }
        $permission_list = preg_split('/[ ]+/', $role->getPermissions());
        $permission_map = ['any' => true];
        foreach ($permission_list as $permission) {
            $permission_map[$permission] = true;
        }
        $this->cached_permission_map_by_role[$role_id] = $permission_map;
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
        $access_token = $this->getParams()['access_token'] ?? null;
        if (!$access_token) {
            return null;
        }
        try {
            return $this->validateAccessToken($access_token);
        } catch (\Exception $exc) {
            return null;
        }
    }

    public function getSessionUser() {
        $auth_username = $this->session()->get('user');
        $user_repo = $this->entityManager()->getRepository(User::class);
        return $user_repo->findOneBy(['username' => $auth_username]);
    }

    public function getAuthenticatedRoles() {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return null;
        }
        return [...$user->getRoles()];
    }

    public function isRoleIdAuthenticated($role_id) {
        $authenticated_roles = $this->getAuthenticatedRoles() ?? [];
        foreach ($authenticated_roles as $authenticated_role) {
            if ($authenticated_role->getId() === $role_id) {
                return true;
            }
        }
        return false;
    }

    public function isUsernameAllowed($username) {
        return preg_match('/^[a-zA-Z0-9-_\\.]+$/', $username) ? true : false;
    }

    public function isPasswordAllowed($password) {
        return strlen($password) >= 8;
    }

    public function generateRandomToken($length = 18) {
        return base64_encode(openssl_random_pseudo_bytes($length));
    }
}
