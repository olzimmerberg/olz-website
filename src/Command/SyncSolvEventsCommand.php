<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\SolvEvent;
use Olz\Parsers\SolvEventParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:sync-solv-events')]
class SyncSolvEventsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function configure(): void {
        $this->addArgument('year', InputArgument::REQUIRED, 'Year (YYYY; 1996 or later)');
    }

    protected $solvEventParser;

    public function __construct() {
        parent::__construct();
        $this->solvEventParser = new SolvEventParser();
    }

    public function setSolvEventParser($solvEventParser) {
        $this->solvEventParser = $solvEventParser;
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $year = $input->getArgument('year');
        if (!preg_match('/^[0-9]{4}$/', $year) || intval($year) < 1996) {
            $this->logAndOutput("Invalid year: {$year}. Must be in format YYYY and 1996 or later.", level: 'notice');
            return Command::INVALID;
        }
        $year = intval($year);
        $this->syncSolvEventsForYear($year);
        return Command::SUCCESS;
    }

    public function syncSolvEventsForYear($year) {
        $this->logAndOutput("Syncing SOLV events for {$year}...");

        $csv = $this->solvFetcher()->fetchEventsCsvForYear($year);

        $csv_excerpt = mb_substr($csv, 0, 255);
        $csv_length = mb_strlen($csv);
        $this->logAndOutput("Successfully read CSV: {$csv_excerpt}... ({$csv_length}).");

        $solv_events = $this->solvEventParser->parse_solv_events_csv($csv);

        $solv_event_count = count($solv_events);
        $this->logAndOutput("Parsed {$solv_event_count} events out of CSV.");

        $this->importSolvEventsForYear($solv_events, $year);
    }

    private function importSolvEventsForYear($solv_events, $year) {
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
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
                    $this->entityManager()->persist($solv_event);
                    $this->entityManager()->flush();
                    $this->logAndOutput("INSERTED {$solv_event->getSolvUid()}");
                } catch (\Exception $e) {
                    $this->logAndOutput("INSERT FAILED {$solv_event->getSolvUid()}: {$e}");
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
                    $this->entityManager()->flush();
                    $this->logAndOutput("UPDATED {$solv_event->getSolvUid()}");
                } catch (\Exception $e) {
                    $this->logAndOutput("UPDATE FAILED {$solv_event->getSolvUid()}: {$e}");
                }
            }
        }
        foreach ($solv_uid_still_exists as $solv_uid => $still_exists) {
            if (!$still_exists) {
                try {
                    $solv_event_repo->deleteBySolvUid($solv_uid);
                    $this->logAndOutput("DELETED {$solv_uid}");
                } catch (\Exception $e) {
                    $this->logAndOutput("DELETE FAILED {$solv_uid}: {$e}");
                }
            }
        }
    }
}
