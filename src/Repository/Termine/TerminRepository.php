<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../../../_/termine/utils/TermineFilterUtils.php';

class TerminRepository extends EntityRepository {
    public function getAllActiveIds() {
        $termine_utils = TermineFilterUtils::fromEnv();
        $is_not_archived = $termine_utils->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('on_off', 1),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        $termin_entries = $this->matching($criteria);
        $termin_entry_ids = [];
        foreach ($termin_entries as $termin_entry) {
            $termin_entry_ids[] = $termin_entry->getId();
        }
        return $termin_entry_ids;
    }
}
