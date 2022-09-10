<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {
    public function findFuzzilyByUsername($username) {
        $dql = "SELECT u FROM Olz:User u WHERE u.username LIKE ?1";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $username);
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
