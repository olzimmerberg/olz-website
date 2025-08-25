<?php

namespace Olz\Repository\Snippets;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Entity\Snippets\Snippet;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Snippet>
 */
class SnippetRepository extends OlzRepository {
    /**
     * @param string[] $terms
     *
     * @return Collection<int, Snippet>&iterable<Snippet>
     */
    public function search(array $terms): Collection {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->contains('text', $term), $terms),
            ))
            ->orderBy([
                'last_modified_at' => Order::Descending,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
