<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {
    public function findFuzzilyByUsername($username) {
        global $db;
        require_once __DIR__.'/../../_/config/database.php';
        $sane_username = $db->escape_string($username);
        $dql = "SELECT u FROM Olz\\Entity\\User u WHERE u.username LIKE '{$sane_username}'";

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getOneOrNullResult();
    }

    public function getUsersWithLogin() {
        $dql = <<<'ZZZZZZZZZZ'
        SELECT u
        FROM Olz\\Entity\\User u
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
