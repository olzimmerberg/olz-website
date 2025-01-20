<?php

namespace Olz\Repository\Roles;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Roles\Role;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Role>
 */
class RoleRepository extends OlzRepository {
    protected string $role_class = Role::class;

    public function getPredefinedRole(PredefinedRole $predefined_role): ?Role {
        $role = $this->findOneBy(['username' => $predefined_role->value]);
        if (!$role) {
            $this->log()->warning("Predefined role does not exist: {$predefined_role->value}");
        }
        return $role;
    }

    public function findRoleFuzzilyByUsername(string $username): ?Role {
        $dql = "SELECT r FROM {$this->role_class} r WHERE r.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
        return $query->getOneOrNullResult();
    }

    public function findRoleFuzzilyByOldUsername(string $old_username): ?Role {
        $dql = "SELECT r FROM {$this->role_class} r WHERE r.old_username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $old_username);
        return $query->getOneOrNullResult();
    }

    /** @return array<Role> */
    public function getRolesWithParent(?int $roleId, int $limit = 100): array {
        if ($roleId === null) {
            $dql = "
                SELECT r
                FROM {$this->role_class} r
                WHERE
                    r.parent_role IS NULL
                    AND r.index_within_parent IS NOT NULL
                    AND r.index_within_parent >= 0
                    AND r.on_off = 1
                ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql);
        } else {
            $dql = "
                SELECT r
                FROM {$this->role_class} r
                WHERE
                    r.parent_role = ?1
                    AND r.index_within_parent IS NOT NULL
                    AND r.index_within_parent >= 0
                    AND r.on_off = 1
                ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $roleId);
        }
        $query->setMaxResults($limit);
        return $query->getResult();
    }

    /** @return Collection<int, Role>&iterable<Role> */
    public function getAllActive(): Collection {
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
