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
        // $qb = $this->createQueryBuilder('t');
        // $qb->join('t.location', 'l')
        //     ->where(
        //         $qb->expr()->andX(
        //             $is_not_archived,
        //             $qb->expr()->eq('t.on_off', 1),
        //             ...array_map(fn ($term) => $qb->expr()->orX(
        //                 $qb->expr()->like('t.title', "%{$term}%"),
        //                 $qb->expr()->like('t.text', "%{$term}%"),
        //                 $qb->expr()->like('l.name', "%{$term}%"),
        //                 $qb->expr()->like('l.details', "%{$term}%"),
        //                 ...$this->searchUtils()->getDateCriteria('t.start_date', $term),
        //                 ...$this->searchUtils()->getDateCriteria('t.end_date', $term),
        //             ), $terms),
        //         )
        //     )
        //     ->orderBy('t.start_date', 'ASC')
        //     ->addOrderBy('t.start_time', 'ASC')
        //     ->setFirstResult(0)
        //     ->setMaxResults(1000000);

        // return $qb->getQuery()->getResult();

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
