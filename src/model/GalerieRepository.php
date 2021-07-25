<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/Galerie.php';
require_once __DIR__.'/../config/doctrine.php';

class GalerieRepository extends EntityRepository {
    public function getAllActiveIds() {
        global $_DATE;
        require_once __DIR__.'/../config/date.php';
        $five_years_ago = $_DATE->getCurrentDateInFormat('Y') - 5;
        $beginning_of_five_years_ago = "{$five_years_ago}-01-01";
        $dql = "
            SELECT ga.id
            FROM Galerie ga
            WHERE ga.on_off='1' AND ga.datum>='{$beginning_of_five_years_ago}'";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();
        return array_map(function ($obj) {
            return $obj['id'];
        }, $result);
    }
}
