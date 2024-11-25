<?php

namespace Olz\Repository;

use Olz\Entity\SolvResult;
use Olz\Repository\Common\OlzRepository;
use Olz\Utils\DbUtils;

/**
 * @extends OlzRepository<SolvResult>
 */
class SolvResultRepository extends OlzRepository {
    protected string $solv_result_class = SolvResult::class;

    /** @return array<SolvResult> */
    public function getUnassignedSolvResults(): array {
        $dql = "SELECT sr FROM {$this->solv_result_class} sr WHERE sr.person = '0'";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /** @return array<array{person: int, name: string, birth_year: string, domicile: string}> */
    public function getAllAssignedSolvResultPersonData(): array {
        $dql = "
            SELECT DISTINCT
                sr.person,
                sr.name,
                sr.birth_year,
                sr.domicile
            FROM {$this->solv_result_class} sr
            WHERE sr.person != '0'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getExactPersonId(SolvResult $solv_result): int {
        $db = DbUtils::fromEnv()->getDb();

        $sane_name = $db->real_escape_string($solv_result->getName());
        $sane_birth_year = $db->real_escape_string($solv_result->getBirthYear());
        $sane_domicile = $db->real_escape_string($solv_result->getDomicile());
        $dql = "
            SELECT sr.person
            FROM {$this->solv_result_class} sr
            WHERE
                sr.name = '{$sane_name}'
                AND sr.birth_year = '{$sane_birth_year}'
                AND sr.domicile = '{$sane_domicile}'
                AND sr.person != '0'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(1);
        try {
            $person_id = $query->getSingleScalarResult();
            return intval($person_id);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function mergePerson(int $old_person_id, int $new_person_id): mixed {
        $sane_old_id = intval($old_person_id);
        $sane_new_id = intval($new_person_id);
        $dql = "
            UPDATE {$this->solv_result_class} sr
            SET sr.person = '{$sane_new_id}'
            WHERE sr.person = '{$sane_old_id}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function solvPersonHasResults(int $id): bool {
        $sane_id = intval($id);
        $dql = "
            SELECT COUNT(sr.id)
            FROM {$this->solv_result_class} sr
            WHERE sr.person = '{$sane_id}'";
        $query = $this->getEntityManager()->createQuery($dql);
        try {
            $count = $query->getSingleScalarResult();
            return intval($count) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
