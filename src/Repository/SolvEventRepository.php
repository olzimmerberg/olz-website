<?php

namespace Olz\Repository;

use Olz\Entity\SolvEvent;
use Olz\Repository\Common\OlzRepository;
use Olz\Utils\DbUtils;

/**
 * @extends OlzRepository<SolvEvent>
 */
class SolvEventRepository extends OlzRepository {
    protected string $solv_event_class = SolvEvent::class;

    /** @return array<SolvEvent> */
    public function getSolvEventsForYear(int $year): array {
        $sane_year = intval($year);
        $sane_next_year = $sane_year + 1;
        $dql = "
            SELECT se
            FROM {$this->solv_event_class} se
            WHERE
                se.date >= '{$sane_year}-01-01'
                AND se.date < '{$sane_next_year}-01-01'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function setResultForSolvEvent(int $solv_uid, string $rank_link): mixed {
        $db = DbUtils::fromEnv()->getDb();

        $sane_solv_uid = intval($solv_uid);
        $sane_rank_link = $db->escape_string($rank_link);
        $dql = "
            UPDATE {$this->solv_event_class} se
            SET se.rank_link = '{$sane_rank_link}'
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function deleteBySolvUid(int $solv_uid): mixed {
        $sane_solv_uid = intval($solv_uid);
        $dql = "
            DELETE {$this->solv_event_class} se
            WHERE se.solv_uid = '{$sane_solv_uid}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }
}
