<?php

require_once __DIR__.'/../../model/User.php';

class AuthUtils {
    protected $entityManager;
    protected $session;

    protected $cached_permission_map_by_user = [];

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setSession($session) {
        $this->session = $session;
    }

    public function hasPermission($query, $user = null) {
        $permission_map = $this->getPermissionMap($user);
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    protected function getPermissionMap($user = null) {
        if (!$user) {
            $user = $this->getSessionUser();
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

    protected function getSessionUser() {
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

    public static function fromEnv() {
        global $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../session/StandardSession.php';
        $session = new StandardSession();
        $auth_utils = new self();
        $auth_utils->setEntityManager($entityManager);
        $auth_utils->setSession($session);
        return $auth_utils;
    }
}
