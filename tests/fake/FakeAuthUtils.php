<?php

require_once __DIR__.'/FakeUsers.php';

class FakeAuthUtils {
    public $authenticate_user;
    public $authenticate_with_error;
    public $has_permission_by_query = [];

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

    public function getAuthenticatedUser() {
        return FakeUsers::adminUser();
    }

    public function getSessionUser() {
        return FakeUsers::adminUser();
    }

    public function isUsernameAllowed($username) {
        return $username !== 'invalid@';
    }

    public function isPasswordAllowed($password) {
        return strlen($password) >= 8;
    }
}
