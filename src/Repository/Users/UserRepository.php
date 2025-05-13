<?php

namespace Olz\Repository\Users;

use Olz\Entity\Users\User;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<User>
 */
class UserRepository extends OlzRepository {
    protected string $entityClass = User::class;

    public function findUserFuzzilyByUsername(string $username): ?User {
        $dql = "SELECT u FROM {$this->entityClass} u WHERE u.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
        return $query->getOneOrNullResult();
    }

    public function findUserFuzzilyByOldUsername(string $old_username): ?User {
        $dql = "SELECT u FROM {$this->entityClass} u WHERE u.old_username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $old_username);
        return $query->getOneOrNullResult();
    }

    public function findUserFuzzilyByName(string $first_name, string $last_name): ?User {
        $dql = "SELECT u FROM {$this->entityClass} u WHERE u.first_name LIKE ?1 AND u.last_name LIKE ?2";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $first_name)->setParameter(2, $last_name)->setMaxResults(1);
        return $query->getOneOrNullResult();
    }

    /** @return array<User> */
    public function getUsersWithLogin(): array {
        $dql = <<<ZZZZZZZZZZ
            SELECT u
            FROM {$this->entityClass} u
            WHERE
                u.email != ''
                AND
                u.email IS NOT NULL
                AND
                u.password != ''
                AND
                u.password IS NOT NULL
            ZZZZZZZZZZ;

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
