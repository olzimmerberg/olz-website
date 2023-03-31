<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeAuthUtils {
    public $current_user;
    public $authenticate_user;
    public $authenticate_with_error;
    public $has_permission_by_query = [];
    public $has_role_permission_by_query = [];

    public function authenticate($username_or_email, $password) {
        if ($this->authenticate_with_error) {
            throw $this->authenticate_with_error;
        }
        return $this->authenticate_user;
    }

    public function hasPermission($query, $user = null) {
        $has_permission = $this->has_permission_by_query[$query] ?? null;
        if ($user !== null && $user->getUsername() === 'no-permission') {
            return false;
        }
        if ($has_permission === null) {
            throw new \Exception("hasPermission has not been mocked for {$query}");
        }
        return $has_permission;
    }

    public function hasRolePermission($query, $role) {
        $has_permission = $this->has_role_permission_by_query[$query] ?? null;
        if ($role !== null && $role->getUsername() === 'no-role-permission') {
            return false;
        }
        if ($has_permission === null) {
            throw new \Exception("hasPermission has not been mocked for {$query}");
        }
        return $has_permission;
    }

    public function getCurrentUser() {
        return $this->current_user;
    }

    public function getSessionUser() {
        return FakeUsers::adminUser();
    }

    public function getAuthenticatedRoles() {
        return [
            FakeRoles::adminRole(),
            FakeRoles::defaultRole(),
        ];
    }

    public function isRoleIdAuthenticated($role_id) {
        if ($role_id === FakeRoles::adminRole()->getId()) {
            return true;
        }
        if ($role_id === FakeRoles::defaultRole()->getId()) {
            return true;
        }
        return false;
    }

    public function isUsernameAllowed($username) {
        return $username !== 'invalid@';
    }

    public function isPasswordAllowed($password) {
        return strlen($password) >= 8;
    }
}
