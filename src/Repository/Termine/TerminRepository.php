<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Termine\Termin;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Termin>
 */
class TerminRepository extends OlzRepository {
    /** @return Collection<int, Termin>&iterable<Termin> */
    public function getAllActive(): Collection {
        $is_not_archived = $this->termineUtils()->getIsNotArchivedCriteria();
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
