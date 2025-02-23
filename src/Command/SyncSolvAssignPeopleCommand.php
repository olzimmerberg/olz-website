<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:sync-solv-assign-people')]
class SyncSolvAssignPeopleCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->assignSolvPeople();
        return Command::SUCCESS;
    }

    public function assignSolvPeople(): void {
        $solv_result_repo = $this->entityManager()->getRepository(SolvResult::class);
        $solv_results = $solv_result_repo->getUnassignedSolvResults();
        foreach ($solv_results as $solv_result) {
            $person = $solv_result_repo->getExactPersonId($solv_result);
            if ($person == 0) {
                $this->logAndOutput("\n---\n");
                $this->logAndOutput("Person not exactly matched: {$solv_result}");
                $person = $this->findOrCreateSolvPerson($solv_result);
            }
            if ($person != 0) {
                $solv_result->setPerson($person);
                $this->occasionallyFlush();
            }
        }
        $this->forceFlush();
    }

    private function findOrCreateSolvPerson(SolvResult $solv_result): int {
        $solv_result_repo = $this->entityManager()->getRepository(SolvResult::class);
        [$solv_result_data, $msg] = $this->generalUtils()->measureLatency(
            function () use ($solv_result_repo) {
                return $solv_result_repo->getAllAssignedSolvResultPersonData();
            }
        );
        $this->logAndOutput("getAllAssignedSolvResultPersonData {$msg}");

        [$person_id, $msg] = $this->generalUtils()->measureLatency(
            function () use ($solv_result, $solv_result_data) {
                return $this->getMatchingPerson(
                    $solv_result->getName(),
                    $solv_result->getBirthYear(),
                    $solv_result->getDomicile(),
                    $solv_result_data
                );
            }
        );
        $this->logAndOutput("getMatchingPerson {$msg}");

        if ($person_id !== null) {
            return $person_id;
        }
        $solv_person = new SolvPerson();
        $solv_person->setSameAs(null);
        $solv_person->setName($solv_result->getName());
        $solv_person->setBirthYear($solv_result->getBirthYear());
        $solv_person->setDomicile($solv_result->getDomicile());
        $solv_person->setMember(1);
        $this->entityManager()->persist($solv_person);
        // This is necessary, s.t. getExactPersonId works correctly for the next iteration.
        $this->forceFlush();
        $insert_id = $solv_person->getId();

        $person_str = json_encode($solv_person, JSON_PRETTY_PRINT) ?: '';
        $this->logAndOutput("Created new person (id {$insert_id}):");
        $this->logAndOutput($person_str);
        return $insert_id;
    }

    /** @param array<array{person: int, name: string, birth_year: string, domicile: string}> $person_infos */
    public function getMatchingPerson(
        string $name,
        string $birth_year,
        string $domicile,
        array $person_infos
    ): ?int {
        $closest_matches = $this->getClosestMatchesOfPersonInfo(
            $name,
            $birth_year,
            $domicile,
            $person_infos,
        );
        $least_difference = $closest_matches['difference'];
        $person_infos_with_least_difference = $closest_matches['matches'];
        $pretty_matches = json_encode($person_infos_with_least_difference, JSON_PRETTY_PRINT);
        $this->logAndOutput("Closest matches (difference {$least_difference}): {$pretty_matches}");
        if ($least_difference >= 3) {
            $this->logAndOutput(" => No matching person found (difference too high).");
            if ($least_difference < 6) {
                $this->logAndOutput("Unclear case. Maybe update logic?", level: 'notice');
            }
            return null;
        }
        $unambiguous_person = $this->getUnambiguousPerson($person_infos_with_least_difference);
        if ($unambiguous_person === null) {
            $this->logAndOutput(" => No matching person found (closest matches contain different persons).");
            return null;
        }
        $this->logAndOutput(" => Matching person found: {$unambiguous_person}.");
        return $unambiguous_person;
    }

    /** @param array<array{person?: int, name?: string, birth_year?: string, domicile?: string}> $person_infos */
    public function getUnambiguousPerson(array $person_infos): ?int {
        if (count($person_infos) == 0) {
            return null;
        }
        $person_id = $person_infos[0]['person'] ?? null;
        if ($person_id === null) {
            return null;
        }
        $suggested_person_id = intval($person_id);
        foreach ($person_infos as $person_info) {
            if (intval($person_info['person'] ?? null) != $suggested_person_id) {
                return null; // there is no unambiguous person
            }
        }
        return $suggested_person_id;
    }

    /**
     * @param array<array{person?: int, name: string, birth_year: string, domicile: string}> $person_infos
     *
     * @return array{difference: int, matches: array<array{person?: int, name: string, birth_year: string, domicile: string}>}
     */
    public function getClosestMatchesOfPersonInfo(
        string $name,
        string $birth_year,
        string $domicile,
        array $person_infos
    ): array {
        $least_difference = strlen($name);
        $person_infos_with_least_difference = [];
        foreach ($person_infos as $row) {
            $difference = $this->getDifferenceBetweenPersonInfo(
                $name,
                $birth_year,
                $domicile,
                $row['name'],
                $row['birth_year'],
                $row['domicile']
            );
            if ($difference < $least_difference) {
                $least_difference = $difference;
                $person_infos_with_least_difference = [$row];
            } elseif ($difference == $least_difference) {
                $person_infos_with_least_difference[] = $row;
            }
        }
        return [
            'difference' => $least_difference,
            'matches' => $person_infos_with_least_difference,
        ];
    }

    public function getDifferenceBetweenPersonInfo(
        string $name_1,
        string $birth_year_1,
        string $domicile_1,
        string $name_2,
        string $birth_year_2,
        string $domicile_2
    ): int {
        $name_difference = levenshtein($name_1, $name_2);
        $int_birth_year_1 = intval($birth_year_1);
        $int_birth_year_2 = intval($birth_year_2);
        $birth_year_difference = levenshtein("{$int_birth_year_1}", "{$int_birth_year_2}");
        $trim_domicile_1 = trim($domicile_1);
        $trim_domicile_2 = trim($domicile_2);
        $domicile_difference = levenshtein($trim_domicile_1, $trim_domicile_2);
        if ($trim_domicile_1 == '' || $trim_domicile_2 == '') {
            $domicile_difference = min($domicile_difference, 2);
        }
        return $name_difference + $birth_year_difference + $domicile_difference;
    }

    protected int $num_updates = 0;
    protected int $flush_every = 1000;

    protected function occasionallyFlush(): void {
        $this->num_updates++;
        if ($this->num_updates > $this->flush_every) {
            $this->forceFlush();
        }
    }

    protected function forceFlush(): void {
        [, $msg] = $this->generalUtils()->measureLatency(function () {
            $this->entityManager()->flush();
            $this->num_updates = 0;
        });
        $this->logAndOutput("forceFlush {$msg}");
    }
}
