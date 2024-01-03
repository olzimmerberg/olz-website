<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;

class SolvPersonRepository extends EntityRepository {
    public function getSolvPersonsMarkedForMerge() {
        $dql = "
            SELECT sp.id, sp.same_as
            FROM Olz:SolvPerson sp
            WHERE sp.same_as IS NOT NULL
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function resetSolvPersonSameAs($id) {
        $sane_id = intval($id);
        $dql = "
            UPDATE Olz:SolvPerson sp
            SET sp.same_as IS NULL
            WHERE sp.id = '{$sane_id}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function deleteById($id) {
        $sane_id = intval($id);
        $dql = "
            DELETE Olz:SolvPerson sp
            WHERE sp.id = '{$sane_id}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }
}
