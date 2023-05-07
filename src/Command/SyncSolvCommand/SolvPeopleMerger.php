<?php

namespace Olz\Command\SyncSolvCommand;

use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Utils\WithUtilsTrait;

class SolvPeopleMerger {
    use WithUtilsTrait;

    public function mergeSolvPeople() {
        $solv_person_repo = $this->entityManager()->getRepository(SolvPerson::class);
        $solv_result_repo = $this->entityManager()->getRepository(SolvResult::class);

        $solv_persons = $solv_person_repo->getSolvPersonsMarkedForMerge();
        foreach ($solv_persons as $row) {
            $id = $row['id'];
            $same_as = $row['same_as'];
            $this->log()->info("Merge person {$id} into {$same_as}.");
            if (intval($same_as) <= 0) {
                $this->log()->warning("Invalid same_as for person {$id}: {$same_as}.");
            } elseif (!$solv_result_repo->solvPersonHasResults($id)) {
                $this->log()->warning("Duplicate person {$id} without any results assigned to merge into person {$same_as}.");
            } else {
                $merge_result = $solv_result_repo->mergePerson($id, $same_as);
                if (!$merge_result) {
                    $this->log()->error("Merge failed! {$merge_result}");
                }
            }
            if (!$solv_result_repo->solvPersonHasResults($id)) {
                $solv_person_repo->deleteById($id);
            } elseif ($id == $same_as) {
                $solv_person_repo->resetSolvPersonSameAs($id);
            } else {
                $this->log()->warning("There are still results assigned to person {$id}.");
            }
        }
    }
}
