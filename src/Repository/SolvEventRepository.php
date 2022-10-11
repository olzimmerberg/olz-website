<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;
use Olz\Utils\DbUtils;

class SolvEventRepository extends EntityRepository {
    public function getSolvEventsForYear($year) {
        $sane_year = intval($year);
        $sane_next_year = $sane_year + 1;
        $dql = "
            SELECT se
            FROM Olz:SolvEvent se
            WHERE
                se.date >= '{$sane_year}-01-01'
                AND se.date < '{$sane_next_year}-01-01'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function setResultForSolvEvent($solv_uid, $rank_link) {
        $db = DbUtils::fromEnv()->getDb();

        $sane_solv_uid = $db->escape_string($solv_uid);
        $sane_rank_link = $db->escape_string($rank_link);
        $dql = "
            UPDATE Olz:SolvEvent se
            SET se.rank_link = '{$sane_rank_link}'
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function deleteBySolvUid($solv_uid) {
        $sane_solv_uid = intval($solv_uid);
        $dql = "
            DELETE Olz:SolvEvent se
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }
}
