<?php

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../model/index.php';

class SolvPeopleMerger {
    use Psr\Log\LoggerAwareTrait;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function mergeSolvPeople() {
        $solv_person_repo = $this->entityManager->getRepository(SolvPerson::class);
        $solv_result_repo = $this->entityManager->getRepository(SolvResult::class);

        $solv_persons = $solv_person_repo->getSolvPersonsMarkedForMerge();
        foreach ($solv_persons as $row) {
            $id = $row['id'];
            $same_as = $row['same_as'];
            $this->logger->info("Merge person {$id} into {$same_as}.");
            if (intval($same_as) <= 0) {
                $this->logger->warning("Invalid same_as for person {$id}: {$same_as}.");
            } elseif (!$solv_result_repo->solvPersonHasResults($id)) {
                $this->logger->warning("Duplicate person {$id} without any results assigned to merge into person {$same_as}.");
            } else {
                $merge_result = $solv_result_repo->mergePerson($id, $same_as);
                if (!$merge_result) {
                    $this->logger->error("Merge failed! {$merge_result}");
                }
            }
            if (!$solv_result_repo->solvPersonHasResults($id)) {
                $solv_person_repo->deleteById($id);
            } elseif ($id == $same_as) {
                $solv_person_repo->resetSolvPersonSameAs($id);
            } else {
                $this->logger->warning("There are still results assigned to person {$id}.");
            }
        }
    }
}
