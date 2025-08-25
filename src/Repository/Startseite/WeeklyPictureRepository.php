<?php

namespace Olz\Repository\Startseite;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<WeeklyPicture>
 */
class WeeklyPictureRepository extends OlzRepository {
    protected string $entityClass = WeeklyPicture::class;

    /** @return array<WeeklyPicture> */
    public function getLatestThree(): array {
        $dql = "
            SELECT wp
            FROM {$this->entityClass} wp
            WHERE wp.on_off = 1
            ORDER BY wp.datum DESC
        ";
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(3);
        return $query->getResult();
    }

    /**
     * @param string[] $terms
     *
     * @return Collection<int, WeeklyPicture>&iterable<WeeklyPicture>
     */
    public function search(array $terms): Collection {
        $archive_threshold = $this->dateUtils()->getIsoArchiveThreshold();
        $is_not_archived = Criteria::expr()->gte('datum', new \DateTime($archive_threshold));
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->contains('text', $term), $terms),
            ))
            ->orderBy([
                'datum' => Order::Descending,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
