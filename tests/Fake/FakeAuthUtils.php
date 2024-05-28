<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Utils\AuthUtils;

class FakeAuthUtils extends AuthUtils {
    public $current_user;
    public $authenticated_roles;
    public $authenticate_user;
    public $authenticate_with_error;
    public $has_permission_by_query = [];
    public $has_role_permission_by_query = [];

    public function authenticate(string $username_or_email, string $password): ?User {
        if ($this->authenticate_with_error) {
            throw $this->authenticate_with_error;
        }
        return $this->authenticate_user;
    }

    public function hasPermission(string $query, ?User $user = null): bool {
        $has_permission = $this->has_permission_by_query[$query] ?? null;
        if ($user !== null && $user->getUsername() === 'no-permission') {
            return false;
        }
        if ($has_permission === null) {
            throw new \Exception("hasPermission has not been mocked for {$query}");
        }
        return $has_permission;
    }

    public function hasRolePermission(string $query, ?Role $role): bool {
        $has_permission = $this->has_role_permission_by_query[$query] ?? null;
        if ($role !== null && $role->getUsername() === 'no-role-permission') {
            return false;
        }
        if ($has_permission === null) {
            throw new \Exception("hasRolePermission has not been mocked for {$query}");
        }
        return $has_permission;
    }

    public function getCurrentUser(): ?User {
        return $this->current_user;
    }

    public function getSessionUser(): ?User {
        return FakeUser::adminUser();
    }

    public function getAuthenticatedRoles(?User $user = null): ?array {
        return $this->authenticated_roles;
    }

    public function isRoleIdAuthenticated(int $role_id): bool {
        if ($role_id === FakeRole::adminRole()->getId()) {
            return true;
        }
        if ($role_id === FakeRole::defaultRole()->getId()) {
            return true;
        }
        return false;
    }

    public function isUsernameAllowed(string $username): bool {
        return $username !== 'invalid@';
    }

    public function isPasswordAllowed(string $password): bool {
        return strlen($password) >= 8;
    }

    public function hashPassword(string $password): string {
        return md5($password); // just for test; security is not a concern
    }

    public function verifyPassword(string $password, string $hash): bool {
        return md5($password) === $hash; // just for test; security is not a concern
    }
}
