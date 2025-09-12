<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
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

    /**
     * @param string[] $terms
     *
     * @return Collection<int, Termin>&iterable<Termin>
     */
    public function search(array $terms): Collection {
        $is_not_archived = $this->termineUtils()->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->orX(
                    Criteria::expr()->contains('title', $term),
                    Criteria::expr()->contains('text', $term),
                    ...$this->searchUtils()->getDateCriteria('start_date', $term),
                    ...$this->searchUtils()->getDateCriteria('end_date', $term),
                ), $terms),
            ))
            ->orderBy([
                'start_date' => Order::Ascending,
                'start_time' => Order::Ascending,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
