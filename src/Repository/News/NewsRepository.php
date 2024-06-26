<?php

namespace Olz\Repository\News;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\News\NewsEntry;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<NewsEntry>
 */
class NewsRepository extends OlzRepository {
    /** @return Collection<int, NewsEntry>&iterable<NewsEntry> */
    public function getAllActive(): Collection {
        $news_utils = NewsFilterUtils::fromEnv();
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
}
