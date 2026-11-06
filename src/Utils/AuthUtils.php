<?php

namespace Olz\Utils;

use Doctrine\ORM\OptimisticLockException;
use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthUtils {
    use WithUtilsTrait;

    public const MAX_LOOP = 5;

    public const REFRESH_TOKEN_COOKIE = 'olz_refresh_token';
    protected const REFRESH_TOKEN_PURPOSE = 'refresh';
    public const REFRESH_TOKEN_TTL = 31536000; // 1 year, slid forward on every use
    /** How long the previous refresh token is still accepted after rotation. */
    public const REFRESH_TOKEN_GRACE = 60; // 1 minute
    public const SESSION_TIMEOUT = 43200; // 12 hours

    /** @var array<int, array<string, bool>> */
    protected array $cached_permission_map_by_user = [];
    /** @var array<int, array<string, bool>> */
    protected array $cached_permission_map_by_role = [];
    /** @var array<string, User> */
    protected array $cached_users = [];

    public function __construct(
        protected ?RequestStack $request_stack = null,
    ) {
    }

    public function authenticate(string $username_or_email, string $password): User {
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
        $hash = $user?->getPasswordHash();
        if (!$user || !$password || !$hash || !$this->verifyPassword($password, $hash)) {
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

    /**
     * Creates a new refresh token for the user and returns the raw token, which
     * must be put into the (HttpOnly) cookie. Only a SHA-256 hash of it is stored.
     * Plain SHA-256 is enough here (unlike passwords) because the token is a
     * high-entropy random value, so there is nothing to brute-force or precompute.
     */
    public function issueRefreshToken(User $user): string {
        $raw_token = $this->generateRefreshToken();
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $access_token = new AccessToken();
        $access_token->setUser($user);
        $access_token->setPurpose(self::REFRESH_TOKEN_PURPOSE);
        $access_token->setToken(hash('sha256', $raw_token));
        $access_token->setCreatedAt($now);
        $access_token->setExpiresAt((clone $now)->modify('+'.self::REFRESH_TOKEN_TTL.' seconds'));
        $this->entityManager()->persist($access_token);
        $this->entityManager()->flush();
        return $raw_token;
    }

    /** Generates a new high-entropy (raw) refresh token. */
    protected function generateRefreshToken(): string {
        return $this->generalUtils()->base64EncodeUrl(openssl_random_pseudo_bytes(32));
    }

    public function revokeAllRefreshTokens(User $user): void {
        $repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_tokens = $repo->findBy([
            'user' => $user,
            'purpose' => self::REFRESH_TOKEN_PURPOSE,
        ]);
        foreach ($access_tokens as $access_token) {
            $this->entityManager()->remove($access_token);
        }
        $this->entityManager()->flush();
    }

    /** Creates the refresh token cookie; `null` creates a cookie that clears it. */
    public function createRefreshTokenCookie(?string $raw_token): Cookie {
        $expires_at = $raw_token === null ? 1 : time() + self::REFRESH_TOKEN_TTL;
        return Cookie::create(self::REFRESH_TOKEN_COOKIE)
            ->withValue($raw_token ?? '')
            ->withExpires($expires_at)
            ->withPath('/')
            ->withSecure($this->request_stack?->getCurrentRequest()?->isSecure() ?? true)
            ->withHttpOnly(true)
            ->withSameSite(Cookie::SAMESITE_LAX)
        ;
    }

    public function getRefreshToken(): ?AccessToken {
        $request = $this->request_stack?->getCurrentRequest();
        $raw_token = $request?->cookies->get(self::REFRESH_TOKEN_COOKIE);
        if (!$raw_token) {
            return null;
        }
        return $this->findRefreshTokenByHash(hash('sha256', $raw_token));
    }

    /**
     * Finds the refresh token row by the hash of the current or of the previous
     * (rotated) raw token.
     */
    public function findRefreshTokenByHash(string $token_hash): ?AccessToken {
        $repo = $this->entityManager()->getRepository(AccessToken::class);
        $refresh_token = $repo->findOneBy([
            'token' => $token_hash,
            'purpose' => self::REFRESH_TOKEN_PURPOSE,
        ]);
        if ($refresh_token !== null) {
            return $refresh_token;
        }
        return $repo->findOneBy([
            'old_token' => $token_hash,
            'purpose' => self::REFRESH_TOKEN_PURPOSE,
        ]);
    }

    /**
     * Restores the session from the given (raw) refresh token, rotating the
     * refresh token in the process. This is used by the refresh API endpoint as
     * well as on the initial page load, so that an expired session can be
     * restored before the page is rendered.
     *
     * Returns the newly issued (raw) refresh token which must be put into the
     * cookie, or null if no cookie change is needed (the session is already
     * valid, or a concurrent request already rotated the token).
     *
     * @throws \Exception if the session cannot be restored
     *                    (missing, unknown, expired, or re-used refresh token)
     */
    public function refreshSessionFromRefreshToken(?string $raw_token): ?string {
        if ($this->session()->has('user')) {
            return null;
        }
        if ($raw_token === null || $raw_token === '') {
            throw new \Exception('No refresh token provided.');
        }
        $token_hash = hash('sha256', $raw_token);
        $refresh_token = $this->findRefreshTokenByHash($token_hash);
        $user = $refresh_token?->getUser();
        if ($refresh_token === null || $user === null) {
            throw new \Exception('Invalid refresh token.');
        }
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $expires_at = $refresh_token->getExpiresAt();
        if ($expires_at !== null && $expires_at < $now) {
            $this->entityManager()->remove($refresh_token);
            $this->entityManager()->flush();
            throw new \Exception('Expired refresh token.');
        }
        if ($refresh_token->getToken() !== $token_hash) {
            // The previous token is only accepted within a short grace window,
            // e.g. for concurrent requests that were in flight when the token
            // was rotated.
            $rotated_at = $refresh_token->getRotatedAt();
            $grace_end = $rotated_at !== null
                ? (clone $rotated_at)->modify('+'.self::REFRESH_TOKEN_GRACE.' seconds')
                : null;
            $is_within_grace = ($grace_end !== null && $grace_end >= $now);
            if (!$is_within_grace) {
                // Re-use of an already rotated token: assume it was stolen.
                $this->entityManager()->remove($refresh_token);
                $this->entityManager()->flush();
                throw new \Exception('Previous refresh token is no longer in the grace window.');
            }
            $this->startSessionForUser($user);
            return null;
        }
        // Rotate the refresh token.
        $new_raw_token = $this->generateRefreshToken();
        $refresh_token->setOldToken($refresh_token->getToken());
        $refresh_token->setToken(hash('sha256', $new_raw_token));
        $refresh_token->setRotatedAt($now);
        $refresh_token->setExpiresAt((clone $now)->modify('+'.self::REFRESH_TOKEN_TTL.' seconds'));
        try {
            $this->entityManager()->flush();
        } catch (OptimisticLockException $exc) {
            // A concurrent request has rotated the token in the meantime. Our
            // token is now the previous one, which is within the grace window.
            $this->entityManager()->detach($refresh_token);
            $current_token = $this->findRefreshTokenByHash($token_hash);
            if ($current_token === null) {
                throw new \Exception('Refresh token disappeared during concurrent rotation.', 0, $exc);
            }
            $this->startSessionForUser($user);
            return null;
        }
        $this->startSessionForUser($user);
        return $new_raw_token;
    }

    protected function startSessionForUser(User $user): void {
        $this->session()->resetConfigure(['timeout' => self::SESSION_TIMEOUT]);
        $this->setSessionUser($user);
        $this->setSessionAuthUser($user);
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
        $user_id = $user->getId() ?: 0;
        $permission_map = $this->cached_permission_map_by_user[$user_id] ?? null;
        if ($permission_map != null) {
            return $permission_map;
        }
        $permission_list = preg_split('/[ ]+/', $user->getPermissions()) ?: [];
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
        if (!$role || !$role->getOnOff()) {
            return ['any' => false];
        }
        $role_id = $role->getId() ?: 0;
        $permission_map = $this->cached_permission_map_by_role[$role_id] ?? null;
        if ($permission_map != null) {
            return $permission_map;
        }
        $permission_list = preg_split('/[ ]+/', $role->getPermissions()) ?: [];
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

    public function setSessionUser(?User $user): void {
        if ($user === null) {
            $this->session()->delete('auth');
            $this->session()->delete('root');
            $this->session()->delete('user_id');
            $this->session()->delete('user');
            $this->session()->delete('user_name');
            $this->session()->delete('user_permissions');
            $this->session()->delete('user_root');
            $this->session()->delete('user_children');
            return;
        }
        // Family
        $user_repo = $this->entityManager()->getRepository(User::class);
        $child_users = $user_repo->findBy(['parent_user' => $user->getId()]);
        $children = [];
        foreach ($child_users as $child_user) {
            $children[] = [
                'id' => $child_user->getId(),
                'permissions' => $child_user->getPermissionMap(),
                'name' => $child_user->getFullName(),
                'username' => $child_user->getUsername(),
                'root' => $child_user->getRoot() !== '' ? $child_user->getRoot() : './',
            ];
        }
        $children_json = json_encode($children) ?: '[]';

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        $permission_map_json = json_encode($user->getPermissionMap()) ?: '{}';
        $this->session()->set('auth', $user->getPermissions()); // TODO: Deprecate
        $this->session()->set('root', $root); // TODO: Deprecate
        $this->session()->set('user_id', "{$user->getId()}");
        $this->session()->set('user', $user->getUsername());
        $this->session()->set('user_name', $user->getFullName());
        $this->session()->set('user_permissions', $permission_map_json);
        $this->session()->set('user_root', $root);
        $this->session()->set('user_children', $children_json);
    }

    public function getSessionAuthUser(): ?User {
        $auth_username = $this->session()->get('auth_user');
        return $this->getUserByUsername($auth_username);
    }

    public function setSessionAuthUser(?User $user): void {
        if ($user === null) {
            $this->session()->delete('auth_user');
            $this->session()->delete('auth_user_id');
            return;
        }
        $this->session()->set('auth_user', $user->getUsername());
        $this->session()->set('auth_user_id', "{$user->getId()}");
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
            if (!$auth_role->getOnOff()) {
                continue;
            }
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
            $role_id = $role?->getParentRoleId() ?? null;
        }
        return false;
    }

    public function isUsernameAllowed(string $username): bool {
        // TODO: Underscore & uppercase is disallowed in frontend, but allowed in backend.
        return preg_match('/^[a-zA-Z0-9-_\.]+$/', $username) ? true : false;
    }

    /** @param ?(User|Role) $existing_entity */
    public function isUsernameUnique(string $username, ?object $existing_entity): bool {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);

        $same_username_user = $user_repo->findOneBy(['username' => $username]);
        $same_old_username_user = $user_repo->findOneBy(['old_username' => $username]);
        $same_username_role = $role_repo->findOneBy(['username' => $username]);
        $same_old_username_role = $role_repo->findOneBy(['old_username' => $username]);

        $has_conflict = (
            ($same_username_user && ($same_username_user !== $existing_entity))
            || ($same_old_username_user && ($same_old_username_user !== $existing_entity))
            || ($same_username_role && ($same_username_role !== $existing_entity))
            || ($same_old_username_role && ($same_old_username_role !== $existing_entity))
        );
        return !$has_conflict;
    }

    public function isPasswordAllowed(string $password): bool {
        return strlen($password) >= 8;
    }

    /** @return array<string, string> */
    public function getUserAvatar(?User $user): array {
        $env_utils = $this->envUtils();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();
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
}
