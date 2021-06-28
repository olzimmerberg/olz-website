<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/NewsEntry.php';
require_once __DIR__.'/../../config/doctrine.php';

class NewsRepository extends EntityRepository {
    public function getAllActiveIds() {
        $dql = "
            SELECT ne.id
            FROM NewsEntry ne
            WHERE ne.on_off='1'";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();
        return array_map(function ($obj) {
            return $obj['id'];
        }, $result);
    }
}
