<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\SolvEvent;
use Olz\Parsers\SolvResultParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:sync-solv-results')]
class SyncSolvResultsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function configure(): void {
        $this->addArgument('year', InputArgument::REQUIRED, 'Year (YYYY; 1996 or later)');
    }
    protected SolvResultParser $solvResultParser;

    public function __construct() {
        parent::__construct();
        $this->solvResultParser = new SolvResultParser();
    }

    public function setSolvResultParser(SolvResultParser $solvResultParser): void {
        $this->solvResultParser = $solvResultParser;
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $year = $input->getArgument('year');
        if (!preg_match('/^[0-9]{4}$/', $year) || intval($year) < 1996) {
            $this->logAndOutput("Invalid year: {$year}. Must be in format YYYY and 1996 or later.", level: 'notice');
            return Command::INVALID;
        }
        $year = intval($year);
        $this->syncSolvResultsForYear($year);
        return Command::SUCCESS;
    }

    public function syncSolvResultsForYear(int $year): void {
        $this->logAndOutput("Syncing SOLV results for {$year}...");
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
        $json = $this->solvFetcher()->fetchYearlyResultsJson($year);

        $json_excerpt = mb_substr($json, 0, 255);
        $json_length = mb_strlen($json);
        $this->logAndOutput("Successfully read JSON: {$json_excerpt}... ({$json_length}).");

        $result_id_by_uid = $this->solvResultParser->parse_solv_yearly_results_json($json);
        $num_results = count($result_id_by_uid);
        $this->logAndOutput("Parsed JSON: {$num_results} results.");

        $existing_solv_events = $solv_event_repo->getSolvEventsForYear($year);
        $num_events = count($existing_solv_events);
        $this->logAndOutput("SOLV events from DB: {$num_events} results.");

        $known_result_index = [];
        foreach ($existing_solv_events as $existing_solv_event) {
            $solv_uid = $existing_solv_event->getSolvUid();
            $rank_link = $existing_solv_event->getRankLink();
            $known_result_index[$solv_uid] = ($rank_link !== null);
        }

        $this->importSolvResultsForYear($result_id_by_uid, $known_result_index);
    }

    /**
     * @param array<int, array{result_list_id: string}> $result_id_by_uid
     * @param array<int, bool>                          $known_result_index
     */
    private function importSolvResultsForYear(
        array $result_id_by_uid,
        array $known_result_index
    ): void {
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
        foreach ($result_id_by_uid as $solv_uid => $event_result) {
            if (!$known_result_index[$solv_uid] && $event_result['result_list_id']) {
                $this->logAndOutput("Event with SOLV ID {$solv_uid} has new results.");
                $html = $this->solvFetcher()->fetchEventResultsHtml($event_result['result_list_id']);
                $results = $this->solvResultParser->parse_solv_event_result_html($html, $solv_uid);
                $results_count = count($results);
                $this->logAndOutput("Number of results fetched & parsed: {$results_count}");
                foreach ($results as $result) {
                    try {
                        $this->entityManager()->persist($result);
                        $this->entityManager()->flush();
                    } catch (\Exception $e) {
                        $this->logAndOutput("Result could not be inserted: {$result->getName()} - {$e->getMessage()}", level: 'warning');
                    }
                }
                $solv_event_repo->setResultForSolvEvent($solv_uid, $event_result['result_list_id']);
            }
        }
    }
}
