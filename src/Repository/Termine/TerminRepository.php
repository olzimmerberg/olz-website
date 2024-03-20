<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Criteria;
use Olz\Repository\Common\OlzRepository;
use Olz\Termine\Utils\TermineFilterUtils;

class TerminRepository extends OlzRepository {
    public function getAllActive() {
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
        return $this->matching($criteria);
    }
}
