<?php

namespace Olz\Repository\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Entity\Service\Download;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Download>
 */
class DownloadRepository extends OlzRepository {
    /**
     * @param string[] $terms
     *
     * @return Collection<int, Download>&iterable<Download>
     */
    public function search(array $terms): Collection {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->contains('name', $term), $terms),
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
