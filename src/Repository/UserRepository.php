<?php

namespace Olz\Repository;

use Olz\Repository\Common\OlzRepository;

class UserRepository extends OlzRepository {
    public function findFuzzilyByUsername($username) {
        $dql = "SELECT u FROM Olz:User u WHERE u.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
        return $query->getOneOrNullResult();
    }

    public function findFuzzilyByOldUsername($old_username) {
        $dql = "SELECT u FROM Olz:User u WHERE u.old_username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $old_username);
        return $query->getOneOrNullResult();
    }

    public function getUsersWithLogin() {
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
