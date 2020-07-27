<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../config/doctrine.php';

class SolvResultRepository extends EntityRepository {
    public function getUnassignedSolvResults() {
        $dql = "SELECT sr FROM SolvResult sr WHERE sr.person = '0'";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getAllAssignedSolvResultPersonData() {
        $dql = "
            SELECT DISTINCT
                sr.person,
                sr.name,
                sr.birth_year,
                sr.domicile
            FROM SolvResult sr
            WHERE sr.person != '0'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getExactPersonId($solv_result) {
        $sane_name = DBEsc($solv_result->getName());
        $sane_birth_year = DBEsc($solv_result->getBirthYear());
        $sane_domicile = DBEsc($solv_result->getDomicile());
        $dql = "
            SELECT sr.person
            FROM SolvResult sr
            WHERE
                sr.name = '{$sane_name}'
                AND sr.birth_year = '{$sane_birth_year}'
                AND sr.domicile = '{$sane_domicile}'
                AND sr.person != '0'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        try {
            $person_id = $query->getSingleScalarResult();
            return intval($person_id);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function mergePerson($old_person_id, $new_person_id) {
        $sane_old_id = intval($old_person_id);
        $sane_new_id = intval($new_person_id);
        $dql = "
            UPDATE SolvResult sr
            SET sr.person = '{$sane_new_id}'
            WHERE sr.person = '{$sane_old_id}'
        ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function solvPersonHasResults($id) {
        $sane_id = intval($id);
        $dql = "
            SELECT COUNT(id)
            FROM SolvResult sr
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
