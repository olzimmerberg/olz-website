<?php

namespace Olz\Command\SyncSolvCommand;

use Olz\Entity\SolvEvent;
use Olz\Parsers\SolvResultParser;
use Olz\Utils\WithUtilsTrait;

class SolvResultsSyncer {
    use WithUtilsTrait;

    protected $solvFetcher;
    protected $solvResultParser;

    public function __construct() {
        $this->solvResultParser = new SolvResultParser();
    }

    public function setSolvFetcher($solvFetcher) {
        $this->solvFetcher = $solvFetcher;
    }

    public function syncSolvResultsForYear($year) {
        $this->log()->info("Syncing SOLV results for {$year}...");
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
        $json = $this->solvFetcher->fetchYearlyResultsJson($year);

        $json_excerpt = substr($json, 0, 255);
        $json_length = strlen($json);
        $this->log()->info("Successfully read JSON: {$json_excerpt}... ({$json_length}).");

        $result_id_by_uid = $this->solvResultParser->parse_solv_yearly_results_json($json);
        $existing_solv_events = $solv_event_repo->getSolvEventsForYear($year);
        $known_result_index = [];
        foreach ($existing_solv_events as $existing_solv_event) {
            $solv_uid = $existing_solv_event->getSolvUid();
            $rank_link = $existing_solv_event->getRankLink();
            $known_result_index[$solv_uid] = ($rank_link !== null) ? 1 : 0;
        }

        $this->importSolvResultsForYear($result_id_by_uid, $known_result_index);
    }

    private function importSolvResultsForYear($result_id_by_uid, $known_result_index) {
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
        $solv_uid_still_exists = [];
        foreach ($result_id_by_uid as $solv_uid => $event_result) {
            if (!$known_result_index[$solv_uid] && $event_result['result_list_id']) {
                $this->log()->info("Event with SOLV ID {$solv_uid} has new results.");
                $html = $this->solvFetcher->fetchEventResultsHtml($event_result['result_list_id']);
                $results = $this->solvResultParser->parse_solv_event_result_html($html, $solv_uid);
                $results_count = count($results);
                $this->log()->info("Number of results fetched & parsed: {$results_count}");
                foreach ($results as $result) {
                    try {
                        $this->entityManager()->persist($result);
                        $this->entityManager()->flush();
                    } catch (\Exception $e) {
                        $this->log()->warning("Result could not be inserted: {$result->getName()} - {$e->getMessage()}");
                    }
                }
                $solv_event_repo->setResultForSolvEvent($solv_uid, $event_result['result_list_id']);
            }
        }
    }
}
