<?php

namespace Olz\Repository\Roles;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository {
    public function findFuzzilyByUsername($username) {
        $dql = "SELECT r FROM Olz:Roles\\Role r WHERE r.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
        return $query->getOneOrNullResult();
    }

    public function findFuzzilyByOldUsername($old_username) {
        $dql = "SELECT r FROM Olz:Roles\\Role r WHERE r.old_username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $old_username);
        return $query->getOneOrNullResult();
    }

    public function getRolesWithParent($roleId, $limit = 100) {
        if ($roleId === null) {
            $dql = "
                SELECT r
                FROM Olz:Roles\\Role r
                WHERE
                    r.parent_role IS NULL
                    AND
                    r.index_within_parent >= 0
                ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql);
        } else {
            $dql = "
                SELECT r
                FROM Olz:Roles\\Role r
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

    public function getAllActive() {
        // TODO: Remove guide != '' condition again, after all ressort
        // descriptions have been updated. This is just temporary logic!
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gte('index_within_parent', 0), // Negative = hidden
                Criteria::expr()->neq('guide', ''),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
