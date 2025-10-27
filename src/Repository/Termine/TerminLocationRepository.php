<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Termine\TerminLocation;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<TerminLocation>
 */
class TerminLocationRepository extends OlzRepository {
    /**
     * @param string[] $terms
     *
     * @return Collection<int, TerminLocation>&iterable<TerminLocation>
     */
    public function search(array $terms): Collection {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->orX(
                    Criteria::expr()->contains('name', $term),
                    Criteria::expr()->contains('details', $term),
                ), $terms),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
