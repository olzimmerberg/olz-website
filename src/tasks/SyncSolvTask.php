<?php

require_once __DIR__.'/common.php';
require_once __DIR__.'/../model/SolvEvent.php';
require_once __DIR__.'/../model/SolvPerson.php';
require_once __DIR__.'/../model/SolvResult.php';
require_once __DIR__.'/../parsers/solv_events.php';
require_once __DIR__.'/../parsers/solv_results.php';

$solv_maintainer_email = 'simon.hatt@olzimmerberg.ch';

class SyncSolvTask extends BackgroundTask {
    public function __construct($entityManager, $solvFetcher) {
        $this->entityManager = $entityManager;
        $this->solvFetcher = $solvFetcher;
    }

    protected static function get_ident() {
        return "SyncSolv";
    }

    protected function run_specific_task() {
        $this->sync_solv_events();
        $this->sync_solv_results();
        $this->sync_solv_people();
        $this->merge_solv_people();
    }

    private function sync_solv_events() {
        $current_year = date('Y');
        $this->sync_solv_events_for_year($current_year);
        $this->sync_solv_events_for_year($current_year - 1);
        $this->sync_solv_events_for_year($current_year + 1);
        $this->sync_solv_events_for_year($current_year - 2);
    }

    private function sync_solv_events_for_year($year) {
        $csv = $this->solvFetcher->fetchEventsCsvForYear($year);
        $solv_events = parse_solv_events_csv($csv);
        $this->import_solv_events_for_year($solv_events, $year);
    }

    private function import_solv_events_for_year($solv_events, $year) {
        $solv_event_repo = $this->entityManager->getRepository(SolvEvent::class);
        $existing_solv_events = $solv_event_repo->getSolvEventsForYear($year);
        $existing_solv_events_index = [];
        foreach ($existing_solv_events as $existing_solv_event) {
            $solv_uid = $existing_solv_event->getSolvUid();
            $existing_solv_events_index[$solv_uid] = $existing_solv_event;
        }
        $solv_uid_still_exists = [];
        foreach ($existing_solv_events_index as $solv_uid => $existing_solv_event) {
            $solv_uid_still_exists[$solv_uid] = false;
        }
        foreach ($solv_events as $solv_event) {
            $solv_uid = $solv_event->getSolvUid();
            $solv_uid_still_exists[$solv_uid] = true;
            $existed = isset($existing_solv_events_index[$solv_uid]);
            $existing_solv_event = $existed ? $existing_solv_events_index[$solv_uid] : null;
            $outdated = $existed ? $solv_event->getLastModification() > $existing_solv_event->getLastModification() : false;
            if (!$existed) {
                try {
                    $this->entityManager->persist($solv_event);
                    $this->entityManager->flush();
                    $this->logger->info("INSERTED {$solv_event->getSolvUid()}");
                } catch (\Exception $e) {
                    $this->logger->info("INSERT FAILED {$solv_event->getSolvUid()}: {$e}");
                }
            } elseif ($outdated) {
                $existing_solv_event->setDate($solv_event->getDate());
                $existing_solv_event->setDuration($solv_event->getDuration());
                $existing_solv_event->setKind($solv_event->getKind());
                $existing_solv_event->setDayNight($solv_event->getDayNight());
                $existing_solv_event->setNational($solv_event->getNational());
                $existing_solv_event->setRegion($solv_event->getRegion());
                $existing_solv_event->setType($solv_event->getType());
                $existing_solv_event->setName($solv_event->getName());
                $existing_solv_event->setLink($solv_event->getLink());
                $existing_solv_event->setClub($solv_event->getClub());
                $existing_solv_event->setMap($solv_event->getMap());
                $existing_solv_event->setLocation($solv_event->getLocation());
                $existing_solv_event->setCoordX($solv_event->getCoordX());
                $existing_solv_event->setCoordY($solv_event->getCoordY());
                $existing_solv_event->setDeadline($solv_event->getDeadline());
                $existing_solv_event->setEntryportal($solv_event->getEntryportal());
                $existing_solv_event->setLastModification($solv_event->getLastModification());
                try {
                    $this->entityManager->flush();
                    $this->logger->info("UPDATED {$solv_event->getSolvUid()}");
                } catch (\Exception $e) {
                    $this->logger->info("UPDATE FAILED {$solv_event->getSolvUid()}: {$e}");
                }
            }
        }
        foreach ($solv_uid_still_exists as $solv_uid => $still_exists) {
            if (!$still_exists) {
                try {
                    $solv_event_repo->deleteBySolvUid($solv_uid);
                    $this->logger->info("DELETED {$solv_uid}");
                } catch (\Exception $e) {
                    $this->logger->info("DELETE FAILED {$solv_uid}: {$e}");
                }
            }
        }
    }

    private function sync_solv_results() {
        $current_year = date('Y');
        $this->sync_solv_results_for_year($current_year);
    }

    private function sync_solv_results_for_year($year) {
        $solv_event_repo = $this->entityManager->getRepository(SolvEvent::class);
        $json = $this->solvFetcher->fetchYearlyResultsJson($year);
        $result_id_by_uid = parse_solv_yearly_results_json($json);
        $existing_solv_events = $solv_event_repo->getSolvEventsForYear($year);
        $known_result_index = [];
        foreach ($existing_solv_events as $existing_solv_event) {
            $solv_uid = $existing_solv_event->getSolvUid();
            $rank_link = $existing_solv_event->getRankLink();
            $known_result_index[$solv_uid] = ($rank_link !== null) ? 1 : 0;
        }
        $this->import_solv_results_for_year($result_id_by_uid, $known_result_index);
    }

    private function import_solv_results_for_year($result_id_by_uid, $known_result_index) {
        $solv_event_repo = $this->entityManager->getRepository(SolvEvent::class);
        $solv_uid_still_exists = [];
        foreach ($result_id_by_uid as $solv_uid => $event_result) {
            if (!$known_result_index[$solv_uid] && $event_result['result_list_id']) {
                $this->logger->info("Event with SOLV ID {$solv_uid} has new results.");
                $html = $this->solvFetcher->fetchEventResultsHtml($event_result['result_list_id']);
                $results = parse_solv_event_result_html($html, $solv_uid);
                $results_count = count($results);
                $this->logger->info("Number of results fetched & parsed: {$results_count}");
                foreach ($results as $result) {
                    try {
                        $this->entityManager->persist($result);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        $this->logger->warning("Result could not be inserted: {$result->getName()}");
                    }
                }
                $solv_event_repo->setResultForSolvEvent($solv_uid, $event_result['result_list_id']);
            }
        }
    }

    private function sync_solv_people() {
        $solv_result_repo = $this->entityManager->getRepository(SolvResult::class);
        $solv_results = $solv_result_repo->getUnassignedSolvResults();
        foreach ($solv_results as $solv_result) {
            $person = $solv_result_repo->getExactPersonId($solv_result);
            if ($person == 0) {
                $this->logger->info("Person not exactly matched:");
                $this->logger->info(json_encode($solv_result, JSON_PRETTY_PRINT));
                $person = $this->find_or_create_solv_person($solv_result);
            }
            if ($person != 0) {
                $solv_result->setPerson($person);
                $this->entityManager->flush();
            }
        }
    }

    private function find_or_create_solv_person($solv_result) {
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

    private function merge_solv_people() {
        $solv_person_repo = $this->entityManager->getRepository(SolvPerson::class);
        $solv_result_repo = $this->entityManager->getRepository(SolvResult::class);

        $solv_persons = $solv_person_repo->getSolvPersonsMarkedForMerge();
        foreach ($solv_persons as $row) {
            $id = $row['id'];
            $same_as = $row['same_as'];
            $this->logger->info("Merge person {$id} into {$same_as}.");
            if (intval($same_as) <= 0) {
                $this->logger->warning("Invalid same_as for person {$id}: {$same_as}.");
            } elseif (!$solv_result_repo->solvPersonHasResults($same_as)) {
                $this->logger->warning("same_as ({$same_as}) without any results assigned for person {$id}.");
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
