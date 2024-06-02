<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:sync-solv-merge-people')]
class SyncSolvMergePeopleCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->mergeSolvPeople();
        return Command::SUCCESS;
    }

    public function mergeSolvPeople(): void {
        $solv_person_repo = $this->entityManager()->getRepository(SolvPerson::class);
        $solv_result_repo = $this->entityManager()->getRepository(SolvResult::class);

        $solv_persons = $solv_person_repo->getSolvPersonsMarkedForMerge();
        foreach ($solv_persons as $row) {
            $id = $row['id'];
            $same_as = $row['same_as'];
            $this->logAndOutput("Merge person {$id} into {$same_as}.");
            if (intval($same_as) <= 0) {
                $this->logAndOutput("Invalid same_as for person {$id}: {$same_as}.", level: 'warning');
            } elseif (!$solv_result_repo->solvPersonHasResults($id)) {
                $this->logAndOutput("Duplicate person {$id} without any results assigned to merge into person {$same_as}.", level: 'warning');
            } else {
                $merge_result = $solv_result_repo->mergePerson($id, $same_as);
                if (!$merge_result) {
                    $this->logAndOutput("Merge failed! {$merge_result}", level: 'error');
                }
            }
            if (!$solv_result_repo->solvPersonHasResults($id)) {
                $solv_person_repo->deleteById($id);
            } elseif ($id == $same_as) {
                $solv_person_repo->resetSolvPersonSameAs($id);
            } else {
                $this->logAndOutput("There are still results assigned to person {$id}.", level: 'warning');
            }
        }
    }
}
