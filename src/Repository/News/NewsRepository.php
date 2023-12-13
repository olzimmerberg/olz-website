<?php

namespace Olz\Repository\News;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Olz\News\Utils\NewsFilterUtils;

class NewsRepository extends EntityRepository {
    public function getAllActive() {
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
