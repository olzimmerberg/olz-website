<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository {
    public function findFuzzilyByUsername($username) {
        $dql = "SELECT r FROM Olz:Role r WHERE r.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
        return $query->getOneOrNullResult();
    }

    public function findFuzzilyByOldUsername($old_username) {
        $dql = "SELECT r FROM Olz:Role r WHERE r.old_username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $old_username);
        return $query->getOneOrNullResult();
    }

    public function getRolesWithParent($roleId, $limit = 100) {
        if ($roleId === null) {
            $dql = "
                SELECT r
                FROM Olz:Role r
                WHERE
                    r.parent_role IS NULL
                    AND
                    r.index_within_parent >= 0
                ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql);
        } else {
            $dql = "
                SELECT r
                FROM Olz:Role r
                WHERE
                    r.parent_role = ?1
                    AND
                    r.index_within_parent >= 0
                ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $roleId);
        }
        $query->setMaxResults($limit);
        return $query->getResult();
    }

    public function getAllActiveRessorts() {
        // TODO: Remove `WHERE r.guide != ''` again, after all ressort
        // descriptions have been updated. This is just temporary logic!
        $dql = "
            SELECT r.username
            FROM Olz:Role r
            WHERE r.guide != ''";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();
        return array_map(function ($obj) {
            return $obj['username'];
        }, $result);
    }
}
