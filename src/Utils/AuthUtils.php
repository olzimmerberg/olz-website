<?php

namespace Olz\Utils;

use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;

class AuthUtils {
    use WithUtilsTrait;

    public const MAX_LOOP = 5;

    /** @var array<string, array<string, bool>> */
    protected array $cached_permission_map_by_user = [];
    /** @var array<string, array<string, bool>> */
    protected array $cached_permission_map_by_role = [];
    /** @var array<string, User> */
    protected array $cached_users = [];

    public function authenticate(string $username_or_email, string $password): ?User {
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);

        // If there are invalid credentials provided too often, we block.
        $num_remaining_attempts = $auth_request_repo->numRemainingAttempts($ip_address);
        if ($num_remaining_attempts <= 0) {
            $message = "Login attempt from blocked IP: {$ip_address} (user: {$username_or_email}).";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'BLOCKED', $username_or_email);
            throw new AuthBlockedException($message);
        }

        $user = $this->resolveUsernameOrEmail($username_or_email);

        // If the password is wrong, authentication fails.
        if (!$user || !$password || !$this->verifyPassword($password, $user->getPasswordHash())) {
            $message = "Login attempt with invalid credentials from IP: {$ip_address} (user: {$username_or_email}).";
            $this->log()->notice($message);
            $auth_request_repo->addAuthRequest($ip_address, 'INVALID_CREDENTIALS', $username_or_email);
            throw new InvalidCredentialsException($message, $num_remaining_attempts);
        }

        $this->log()->info("User login successful: {$username_or_email}");
        $this->log()->info("  Auth: {$user->getPermissions()}");
        $this->log()->info("  Root: {$user->getRoot()}");
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED', $username_or_email);
        return $user;
    }

    public function validateAccessToken(string $access_token): ?User {
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

    public function resolveUsernameOrEmail(string $username_or_email): ?User {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $username_or_email]);
        if (!$user) {
            $user = $user_repo->findOneBy(['email' => $username_or_email]);
        }
        if (!$user) {
            $user = $user_repo->findOneBy(['old_username' => $username_or_email]);
        }
        $res = preg_match(
            '/^([a-zA-Z0-9-_\.]+)@olzimmerberg.ch$/',
            $username_or_email,
            $matches
        );
        if (!$user && $res) {
            $user = $user_repo->findOneBy(['username' => $matches[1]]);
        }
        if (!$user && $res) {
            $user = $user_repo->findOneBy(['old_username' => $matches[1]]);
        }
        return $user;
    }

    public function hasPermission(string $query, ?User $user = null): bool {
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        $user_permission_map = $this->getUserPermissionMap($user);
        $roles = $this->getAuthenticatedRoles($user) ?? [];
        $permission_map = [...$user_permission_map];
        foreach ($roles as $role) {
            $role_permission_map = $this->getRolePermissionMap($role);
            $permission_map = [...$role_permission_map, ...$permission_map];
        }
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    public function hasUserPermission(string $query, ?User $user): bool {
        $permission_map = $this->getUserPermissionMap($user);
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    /** @return array<string, bool> */
    protected function getUserPermissionMap(?User $user): array {
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

    public function hasRolePermission(string $query, ?Role $role): bool {
        $permission_map = $this->getRolePermissionMap($role);
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    /** @return array<string, bool> */
    protected function getRolePermissionMap(?Role $role): array {
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

    public function getCurrentUser(): ?User {
        $user = $this->getTokenUser();
        if ($user) {
            return $user;
        }
        return $this->getSessionUser();
    }

    public function getCurrentAuthUser(): ?User {
        return $this->getSessionAuthUser();
    }

    public function getTokenUser(): ?User {
        $access_token = $this->getParams()['access_token'] ?? null;
        if (!$access_token) {
            return null;
        }
        try {
            return $this->validateAccessToken($access_token);
        } catch (AuthBlockedException $exc) {
            return null;
        } catch (InvalidCredentialsException $exc) {
            return null;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    public function getSessionUser(): ?User {
        $username = $this->session()->get('user');
        return $this->getUserByUsername($username);
    }

    public function getSessionAuthUser(): ?User {
        $auth_username = $this->session()->get('auth_user');
        return $this->getUserByUsername($auth_username);
    }

    private function getUserByUsername(?string $username): ?User {
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

    /** @return ?array<Role> */
    public function getAuthenticatedRoles(?User $user = null): ?array {
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        if (!$user) {
            return null;
        }
        return [...$user->getRoles()];
    }

    public function isRoleIdAuthenticated(int $role_id): bool {
        $authenticated_roles = $this->getAuthenticatedRoles() ?? [];
        foreach ($authenticated_roles as $authenticated_role) {
            if ($authenticated_role->getId() === $role_id) {
                return true;
            }
        }
        return false;
    }

    public function hasRoleEditPermission(?int $role_id = null): bool {
        $user = $this->getCurrentUser();
        if ($this->hasPermission('roles', $user)) {
            return true;
        }
        $auth_roles = $this->getAuthenticatedRoles($user);
        $is_role_id_authenticated = [];
        foreach (($auth_roles ?? []) as $auth_role) {
            $is_role_id_authenticated[$auth_role->getId()] = true;
        }
        $role_repo = $this->entityManager()->getRepository(Role::class);
        for ($i = 0; $i < self::MAX_LOOP; $i++) {
            if ($role_id === null) {
                return false;
            }
            if (($is_role_id_authenticated[$role_id] ?? false) === true) {
                return true;
            }
            $role = $role_repo->findOneBy(['id' => $role_id]);
            $role_id = $role->getParentRoleId() ?? null;
        }
        return false;
    }

    public function isUsernameAllowed(string $username): bool {
        return preg_match('/^[a-zA-Z0-9-_\.]+$/', $username) ? true : false;
    }

    public function isPasswordAllowed(string $password): bool {
        return strlen($password) >= 8;
    }

    /** @return array<string, string> */
    public function getUserAvatar(?User $user): array {
        $env_utils = $this->envUtils();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();
        $data_path = $env_utils->getDataPath();
        if (!$user) {
            $initials_enc = urlencode('?');
            return ['1x' => "{$code_href}assets/user_initials_{$initials_enc}.svg"];
        }
        if ($user->getAvatarImageId()) {
            $image_id = $user->getAvatarImageId();
            return [
                '2x' => "{$data_href}img/users/{$user->getId()}/thumb/{$image_id}\$256.jpg",
                '1x' => "{$data_href}img/users/{$user->getId()}/thumb/{$image_id}\$128.jpg",
            ];
        }
        $user_images = [];
        $user_image_path_2x = "img/users/{$user->getId()}@2x.jpg";
        if (is_file("{$data_path}{$user_image_path_2x}")) {
            $user_images['2x'] = "{$data_href}{$user_image_path_2x}";
        }
        $user_image_path = "img/users/{$user->getId()}.jpg";
        if (is_file("{$data_path}{$user_image_path}")) {
            $user_images['1x'] = "{$data_href}{$user_image_path}";
        }
        if (count($user_images) > 0) {
            return $user_images;
        }
        $first_initial = mb_substr($user->getFirstName(), 0, 1);
        $last_initial = mb_substr($user->getLastName(), 0, 1);
        $initials_enc = urlencode(strtoupper("{$first_initial}{$last_initial}"));
        return ['1x' => "{$code_href}assets/user_initials_{$initials_enc}.svg"];
    }

    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public static function fromEnv(): self {
        return new self();
    }
}
