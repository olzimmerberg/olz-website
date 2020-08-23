<?php

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../model/SolvPerson.php';
require_once __DIR__.'/../../model/SolvResult.php';

class SolvPeopleAssigner {
    use Psr\Log\LoggerAwareTrait;

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

        $least_difference = strlen($solv_result->getName());
        $rows_with_least_difference = [];
        foreach ($solv_result_data as $row) {
            $name_difference = levenshtein($solv_result->getName(), $row['name']);
            $int_birth_year = intval($solv_result->getBirthYear());
            $int_birth_year_row = intval($row['birth_year']);
            $birth_year_difference = levenshtein("{$int_birth_year}", "{$int_birth_year_row}");
            $trim_domicile = trim($solv_result->getDomicile());
            $trim_domicile_row = trim($row['domicile']);
            $domicile_difference = levenshtein($trim_domicile, $trim_domicile_row);
            if ($trim_domicile == '' || $trim_domicile_row == '') {
                $domicile_difference = min($domicile_difference, 2);
            }
            $difference = $name_difference + $birth_year_difference + $domicile_difference;
            if ($difference < $least_difference) {
                $least_difference = $difference;
                $rows_with_least_difference = [$row];
            } elseif ($difference == $least_difference) {
                $rows_with_least_difference[] = $row;
            }
        }
        if ($least_difference < 3 && count($rows_with_least_difference) == 1) {
            $this->logger->info("Fuzzily matched persons (difference {$least_difference}, take first):");
            $this->logger->info(json_encode($rows_with_least_difference, JSON_PRETTY_PRINT));
            return intval($rows_with_least_difference[0]['person']);
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
        $closest_matches_str = json_encode($rows_with_least_difference, JSON_PRETTY_PRINT);
        $this->logger->info("Created new person (id {$insert_id}):");
        $this->logger->info($person_str);
        $this->logger->info("Closest matches (difference {$least_difference}) were:");
        $this->logger->info($closest_matches_str);
        if ($least_difference < 6 && count($rows_with_least_difference) > 0) {
            $this->logger->info("Unclear case. TODO: Send mail in this case.");
        }
        return $insert_id;
    }
}
