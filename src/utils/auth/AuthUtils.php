<?php

require_once __DIR__.'/../../model/User.php';

class AuthUtils {
    protected $entityManager;
    protected $session;

    protected $cached_permission_map;

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setSession($session) {
        $this->session = $session;
    }

    public function hasPermission($query) {
        $permission_map = $this->getPermissionMap();
        return ($permission_map['all'] ?? false) || ($permission_map[$query] ?? false);
    }

    protected function getPermissionMap() {
        if ($this->cached_permission_map != null) {
            return $this->cached_permission_map;
        }
        $auth_username = $this->session->get('user');
        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $auth_username]);
        if (!$user) {
            return ['any' => false];
        }
        $permission_list = preg_split('/[ ]+/', $user->getZugriff());
        $permission_map = ['any' => true];
        foreach ($permission_list as $permission) {
            $permission_map[$permission] = true;
        }
        $this->cached_permission_map = $permission_map;
        return $permission_map;
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
