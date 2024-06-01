<?php

namespace Olz\Repository;

use Olz\Entity\User;
use Olz\Repository\Common\OlzRepository;

class UserRepository extends OlzRepository {
    public function findFuzzilyByUsername(string $username): ?User {
        $dql = "SELECT u FROM Olz:User u WHERE u.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
        return $query->getOneOrNullResult();
    }

    public function findFuzzilyByOldUsername(string $old_username): ?User {
        $dql = "SELECT u FROM Olz:User u WHERE u.old_username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $old_username);
        return $query->getOneOrNullResult();
    }

    /** @return array<User> */
    public function getUsersWithLogin(): array {
        $dql = <<<'ZZZZZZZZZZ'
            SELECT u
            FROM Olz:User u
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
