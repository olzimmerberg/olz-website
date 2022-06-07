<?php

use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;

class SolvPeopleAssigner {
    use \Psr\Log\LoggerAwareTrait;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function assignSolvPeople() {
        $solv_result_repo = $this->entityManager->getRepository(SolvResult::class);
        $solv_results = $solv_result_repo->getUnassignedSolvResults();
        foreach ($solv_results as $solv_result) {
            $person = $solv_result_repo->getExactPersonId($solv_result);
            if ($person == 0) {
                $this->logger->info("Person not exactly matched:");
                $this->logger->info(json_encode($solv_result, JSON_PRETTY_PRINT));
                $person = $this->findOrCreateSolvPerson($solv_result);
            }
            if ($person != 0) {
                $solv_result->setPerson($person);
                $this->entityManager->flush();
            }
        }
    }

    private function findOrCreateSolvPerson($solv_result) {
        $solv_result_repo = $this->entityManager->getRepository(SolvResult::class);
        $solv_result_data = $solv_result_repo->getAllAssignedSolvResultPersonData();

        $person_id = $this->getMatchingPerson(
            $solv_result->getName(),
            $solv_result->getBirthYear(),
            $solv_result->getDomicile(),
            $solv_result_data
        );
        if ($person_id !== null) {
            return $person_id;
        }
        $solv_person = new SolvPerson();
        $solv_person->setSameAs(null);
        $solv_person->setName($solv_result->getName());
        $solv_person->setBirthYear($solv_result->getBirthYear());
        $solv_person->setDomicile($solv_result->getDomicile());
        $solv_person->setMember(1);
        $this->entityManager->persist($solv_person);
        $this->entityManager->flush();
        $insert_id = $solv_person->getId();

        $person_str = json_encode($solv_person, JSON_PRETTY_PRINT);
        $this->logger->info("Created new person (id {$insert_id}):");
        $this->logger->info($person_str);
        return $insert_id;
    }

    public function getMatchingPerson(
        $name,
        $birth_year,
        $domicile,
        $person_infos
    ) {
        $closest_matches = $this->getClosestMatchesOfPersonInfo(
            $name,
            $birth_year,
            $domicile,
            $person_infos,
        );
        $least_difference = $closest_matches['difference'];
        $person_infos_with_least_difference = $closest_matches['matches'];
        $pretty_matches = json_encode($person_infos_with_least_difference, JSON_PRETTY_PRINT);
        $this->logger->info("Closest matches (difference {$least_difference}): {$pretty_matches}");
        if ($least_difference >= 3) {
            $this->logger->info(" => No matching person found (difference too high).");
            if ($least_difference < 6) {
                $this->logger->notice("Unclear case. Maybe update logic?");
            }
            return null;
        }
        $unambiguous_person = $this->getUnambiguousPerson($person_infos_with_least_difference);
        if ($unambiguous_person === null) {
            $this->logger->info(" => No matching person found (closest matches contain different persons).");
            return null;
        }
        $this->logger->info(" => Matching person found: {$unambiguous_person}.");
        return $unambiguous_person;
    }

    public function getUnambiguousPerson($person_infos) {
        if (count($person_infos) == 0) {
            return null;
        }
        $suggested_person_id = intval($person_infos[0]['person']);
        foreach ($person_infos as $person_info) {
            if (intval($person_info['person']) != $suggested_person_id) {
                return null; // there is no unambiguous person
            }
        }
        return $suggested_person_id;
    }

    public function getClosestMatchesOfPersonInfo(
        $name,
        $birth_year,
        $domicile,
        $person_infos
    ) {
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
        $name_1,
        $birth_year_1,
        $domicile_1,
        $name_2,
        $birth_year_2,
        $domicile_2
    ) {
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
}
