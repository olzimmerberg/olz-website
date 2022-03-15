<?php

require_once __DIR__.'/../model/OlzEntity.php';

class EntityUtils {
    use Psr\Log\LoggerAwareTrait;

    public function setAuthUtils($authUtils) {
        $this->authUtils = $authUtils;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function createOlzEntity(OlzEntity $entity, $input) {
        $user_repo = $this->entityManager->getRepository(User::class);
        $role_repo = $this->entityManager->getRepository(Role::class);
        $current_user = $this->authUtils->getSessionUser();
        $now_datetime = new DateTime($this->dateUtils->getIsoNow());

        $on_off = $input['onOff'] ? 1 : 0;

        $owner_user_id = $input['ownerUserId'] ?? null;
        $owner_user = $current_user;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        $owner_role = null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
        }

        $entity->setOnOff($on_off);
        $entity->setOwnerUser($owner_user);
        $entity->setOwnerRole($owner_role);
        $entity->setCreatedAt($now_datetime);
        $entity->setCreatedByUser($current_user);
        $entity->setLastModifiedAt($now_datetime);
        $entity->setLastModifiedByUser($current_user);
    }

    public function updateOlzEntity(OlzEntity $entity, $input) {
        $user_repo = $this->entityManager->getRepository(User::class);
        $role_repo = $this->entityManager->getRepository(Role::class);
        $current_user = $this->authUtils->getSessionUser();
        $now_datetime = new DateTime($this->dateUtils->getIsoNow());

        $on_off = $input['onOff'] ? 1 : 0;

        $owner_user_id = $input['ownerUserId'] ?? null;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
            $entity->setOwnerUser($owner_user);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
            $entity->setOwnerRole($owner_role);
        }

        $entity->setOnOff($on_off);
        $entity->setLastModifiedAt($now_datetime);
        $entity->setLastModifiedByUser($current_user);
    }

    public static function fromEnv() {
        global $entityManager;
        require_once __DIR__.'/../config/doctrine_db.php';
        require_once __DIR__.'/auth/AuthUtils.php';
        require_once __DIR__.'/date/DateUtils.php';
        require_once __DIR__.'/env/EnvUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $date_utils = DateUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
        $entity_utils = new self();
        $entity_utils->setAuthUtils($auth_utils);
        $entity_utils->setDateUtils($date_utils);
        $entity_utils->setEntityManager($entityManager);
        $entity_utils->setLogger($logger);
        return $entity_utils;
    }
}
