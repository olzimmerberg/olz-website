<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../config/doctrine.php';

class UserRepository extends EntityRepository {
    public function getUsersForRole($roleId) {
        $dql = "SELECT r, u FROM Role r JOIN r.users u ORDER BY u.first_name, u.last_name ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function findFuzzilyByUsername($username) {
        $sane_username = DBEsc($username);
        $dql = "SELECT u FROM User u WHERE u.username LIKE '{$sane_username}'";

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getOneOrNullResult();
    }
}
