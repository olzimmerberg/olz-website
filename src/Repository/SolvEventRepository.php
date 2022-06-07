<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;

class SolvEventRepository extends EntityRepository {
    public function getSolvEventsForYear($year) {
        $sane_year = intval($year);
        $sane_next_year = $sane_year + 1;
        $dql = "
            SELECT se
            FROM Olz\\Entity\\SolvEvent se
            WHERE
                se.date >= '{$sane_year}-01-01'
                AND se.date < '{$sane_next_year}-01-01'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function setResultForSolvEvent($solv_uid, $rank_link) {
        global $db;
        require_once __DIR__.'/../../_/config/database.php';
        $sane_solv_uid = $db->escape_string($solv_uid);
        $sane_rank_link = $db->escape_string($rank_link);
        $dql = "
            UPDATE Olz\\Entity\\SolvEvent se
            SET se.rank_link = '{$sane_rank_link}'
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function deleteBySolvUid($solv_uid) {
        $sane_solv_uid = intval($solv_uid);
        $dql = "
            DELETE Olz\\Entity\\SolvEvent se
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }
}
