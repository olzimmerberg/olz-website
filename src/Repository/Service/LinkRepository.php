<?php

namespace Olz\Repository\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Entity\Service\Link;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Link>
 */
class LinkRepository extends OlzRepository {
    /**
     * @param string[] $terms
     *
     * @return Collection<int, Link>&iterable<Link>
     */
    public function search(array $terms): Collection {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->orX(
                    Criteria::expr()->contains('name', $term),
                    Criteria::expr()->contains('url', $term),
                ), $terms),
            ))
            ->orderBy([
                'position' => Order::Ascending,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
