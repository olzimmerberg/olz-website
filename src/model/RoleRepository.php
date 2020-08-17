<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../config/doctrine.php';

class RoleRepository extends EntityRepository {
    public function getRolesWithParent($roleId, $limit = 100) {
        if ($roleId === null) {
            $dql = "SELECT r FROM Role r WHERE r.parent_role IS NULL ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql);
        } else {
            $dql = "SELECT r FROM Role r WHERE r.parent_role = ?1 ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $roleId);
        }
        $query->setMaxResults($limit);
        return $query->getResult();
    }
}
