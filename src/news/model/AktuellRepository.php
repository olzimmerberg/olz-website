<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/Aktuell.php';
require_once __DIR__.'/../../config/doctrine.php';

class AktuellRepository extends EntityRepository {
    public function getAllActiveIds() {
        $dql = "
            SELECT ak.id
            FROM Aktuell ak
            WHERE ak.on_off='1'";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();
        return array_map(function ($obj) {
            return $obj['id'];
        }, $result);
    }
}
