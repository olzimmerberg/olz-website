<?php

namespace Olz\Repository;

use Olz\Entity\SolvPerson;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<SolvPerson>
 */
class SolvPersonRepository extends OlzRepository {
    protected string $solv_person_class = SolvPerson::class;

    /** @return array<array{id: int, same_as: ?int}> */
    public function getSolvPersonsMarkedForMerge() {
        $dql = "
            SELECT sp.id, sp.same_as
            FROM {$this->solv_person_class} sp
            WHERE sp.same_as IS NOT NULL
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function resetSolvPersonSameAs(int $id): mixed {
        $sane_id = intval($id);
        $dql = "
            UPDATE {$this->solv_person_class} sp
            SET sp.same_as IS NULL
            WHERE sp.id = '{$sane_id}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function deleteById(int $id): mixed {
        $sane_id = intval($id);
        $dql = "
            DELETE {$this->solv_person_class} sp
            WHERE sp.id = '{$sane_id}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }
}
