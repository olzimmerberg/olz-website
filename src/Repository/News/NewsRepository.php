<?php

namespace Olz\Repository\News;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Entity\News\NewsEntry;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<NewsEntry>
 */
class NewsRepository extends OlzRepository {
    /** @return Collection<int, NewsEntry>&iterable<NewsEntry> */
    public function getAllActive(): Collection {
        $news_utils = $this->newsUtils();
        $is_not_archived = $news_utils->getIsNotArchivedCriteria();
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
     * @return Collection<int, NewsEntry>&iterable<NewsEntry>
     */
    public function search(array $terms): Collection {
        $news_utils = $this->newsUtils();
        $is_not_archived = $news_utils->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->orX(
                    Criteria::expr()->contains('title', $term),
                    Criteria::expr()->contains('teaser', $term),
                    Criteria::expr()->contains('content', $term),
                    ...$this->searchUtils()->getDateCriteria('published_date', $term),
                ), $terms),
            ))
            ->orderBy([
                'published_date' => Order::Descending,
                'published_time' => Order::Descending,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
