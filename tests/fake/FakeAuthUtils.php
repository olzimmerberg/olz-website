<?php

require_once __DIR__.'/fake_user.php';

class FakeAuthUtils {
    public $has_permission_by_query = [];

    public function hasPermission($query, $user = null) {
        $has_permission = $this->has_permission_by_query[$query] ?? null;
        if ($user !== null && $user->getUsername() === 'no-permission') {
            return false;
        }
        if ($has_permission === null) {
            throw new Exception("hasPermission has not been mocked for {$query}");
        }
        return $has_permission;
    }

    public function getSessionUser() {
        return get_fake_user();
    }

    public function isUsernameAllowed($username) {
        return $username !== 'invalid@';
    }

    public function isPasswordAllowed($password) {
        return strlen($password) >= 8;
    }
}
