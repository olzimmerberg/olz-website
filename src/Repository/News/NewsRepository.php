<?php

namespace Olz\Repository\News;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../../../_/news/utils/NewsFilterUtils.php';

class NewsRepository extends EntityRepository {
    public function getAllActiveIds() {
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
        $news_entries = $this->matching($criteria);
        $news_entry_ids = [];
        foreach ($news_entries as $news_entry) {
            $news_entry_ids[] = $news_entry->getId();
        }
        return $news_entry_ids;
    }
}
