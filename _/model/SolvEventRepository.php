<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../config/doctrine.php';

class SolvEventRepository extends EntityRepository {
    public function getSolvEventsForYear($year) {
        $sane_year = intval($year);
        $sane_next_year = $sane_year + 1;
        $dql = "
            SELECT se
            FROM SolvEvent se
            WHERE
                se.date >= '{$sane_year}-01-01'
                AND se.date < '{$sane_next_year}-01-01'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function setResultForSolvEvent($solv_uid, $rank_link) {
        $sane_solv_uid = DBEsc($solv_uid);
        $sane_rank_link = DBEsc($rank_link);
        $dql = "
            UPDATE SolvEvent se
            SET se.rank_link = '{$sane_rank_link}'
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function deleteBySolvUid($solv_uid) {
        $sane_solv_uid = intval($solv_uid);
        $dql = "
            DELETE SolvEvent se
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }
}
