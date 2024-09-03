<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Termine\Termin;
use Olz\Repository\Common\IdentStringRepositoryInterface;
use Olz\Repository\Common\IdentStringRepositoryTrait;
use Olz\Repository\Common\OlzRepository;
use Olz\Termine\Utils\TermineFilterUtils;

/**
 * @extends OlzRepository<Termin>
 *
 * @implements IdentStringRepositoryInterface<Termin>
 */
class TerminRepository extends OlzRepository implements IdentStringRepositoryInterface {
    /** @use IdentStringRepositoryTrait<Termin> */
    use IdentStringRepositoryTrait;

    /** @return Collection<int, Termin>&iterable<Termin> */
    public function getAllActive(): Collection {
        $termine_utils = TermineFilterUtils::fromEnv();
        $is_not_archived = $termine_utils->getIsNotArchivedCriteria();
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
