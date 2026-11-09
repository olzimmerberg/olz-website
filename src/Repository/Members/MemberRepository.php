<?php

namespace Olz\Repository\Members;

use Olz\Entity\Members\Member;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Member>
 */
class MemberRepository extends OlzRepository {
    protected string $entityClass = Member::class;

    /**
     * All idents of members which are not (soft-)deleted.
     *
     * @return array<string>
     */
    public function getActiveIdents(): array {
        $dql = "
            SELECT r.ident
            FROM {$this->entityClass} r
            WHERE r.deleted_at IS NULL
            ORDER BY r.ident ASC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(null);
        return array_map(fn ($item) => $item['ident'], $query->getResult());
    }
}
