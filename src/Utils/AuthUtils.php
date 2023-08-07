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
        'envUtils',
        'getParams',
        'log',
        'server',
        'session',
    ];

    protected $cached_permission_map_by_user = [];
    protected $cached_permission_map_by_role = [];
    protected $cached_users = [];

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
        $user_permission_map = $this->getUserPermissionMap($user);
        $roles = $this->getAuthenticatedRoles($user) ?? [];
        $permission_map = [...$user_permission_map];
        foreach ($roles as $role) {
            $role_permission_map = $this->getRolePermissionMap($role);
            $permission_map = [...$role_permission_map, ...$permission_map];
        }
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    public function hasUserPermission($query, $user = null) {
        $permission_map = $this->getUserPermissionMap($user);
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    protected function getUserPermissionMap($user = null) {
        if (!$user) {
            $user = $this->getCurrentUser();
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

    public function getCurrentUser() {
        $user = $this->getTokenUser();
        if ($user) {
            return $user;
        }
        return $this->getSessionUser();
    }

    public function getCurrentAuthUser() {
        return $this->getSessionAuthUser();
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
        $username = $this->session()->get('user');
        return $this->getUserByUsername($username);
    }

    public function getSessionAuthUser() {
        $auth_username = $this->session()->get('auth_user');
        return $this->getUserByUsername($auth_username);
    }

    private function getUserByUsername(?string $username) {
        if (!$username) {
            return null;
        }
        $cached_user = $this->cached_users[$username] ?? null;
        if ($cached_user) {
            return $cached_user;
        }
        $user_repo = $this->entityManager()->getRepository(User::class);
        $fetched_user = $user_repo->findOneBy(['username' => $username]);
        if ($fetched_user) {
            $this->cached_users[$username] = $fetched_user;
        }
        return $fetched_user;
    }

    public function getAuthenticatedRoles() {
        $user = $this->getCurrentUser();
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

    public function getUserAvatar(?User $user) {
        $env_utils = $this->envUtils();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();
        $data_path = $env_utils->getDataPath();
        if (!$user) {
            $initials_enc = urlencode('?');
            return "{$data_href}assets/user_initials_{$initials_enc}.svg";
        }
        $user_image_path = "img/users/{$user->getId()}.jpg";
        if (is_file("{$data_path}{$user_image_path}")) {
            return "{$data_href}{$user_image_path}";
        }
        $first_initial = mb_substr($user->getFirstName() ?? '?', 0, 1);
        $last_initial = mb_substr($user->getLastName() ?? '?', 0, 1);
        $initials_enc = urlencode(strtoupper("{$first_initial}{$last_initial}"));
        return "{$data_href}assets/user_initials_{$initials_enc}.svg";
    }
}
