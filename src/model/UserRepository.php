<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../config/doctrine.php';

class UserRepository extends EntityRepository {
    public function findFuzzilyByUsername($username) {
        $sane_username = DBEsc($username);
        $dql = "SELECT u FROM User u WHERE u.username LIKE '{$sane_username}'";

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getOneOrNullResult();
    }

    public function getUsersWithEmail() {
        $dql = <<<'ZZZZZZZZZZ'
        SELECT u
        FROM User u
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
