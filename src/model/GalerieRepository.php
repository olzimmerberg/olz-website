<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/Galerie.php';
require_once __DIR__.'/../config/doctrine.php';

class GalerieRepository extends EntityRepository {
    public function getAllActiveIds() {
        $dql = "
            SELECT ga.id
            FROM Galerie ga
            WHERE ga.on_off='1'";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();
        return array_map(function ($obj) {
            return $obj['id'];
        }, $result);
    }
}
