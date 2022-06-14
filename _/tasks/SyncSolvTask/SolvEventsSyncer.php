<?php

use Olz\Entity\SolvEvent;
use Olz\Parsers\SolvEventParser;

class SolvEventsSyncer {
    use \Psr\Log\LoggerAwareTrait;

    public function __construct($entityManager, $solvFetcher) {
        $this->entityManager = $entityManager;
        $this->solvFetcher = $solvFetcher;
        $this->solvEventParser = new SolvEventParser();
    }

    public function syncSolvEventsForYear($year) {
        $this->logger->info("Syncing SOLV events for {$year}...");

        $csv = $this->solvFetcher->fetchEventsCsvForYear($year);

        $csv_excerpt = substr($csv, 0, 255);
        $csv_length = strlen($csv);
        $this->logger->info("Successfully read CSV: {$csv_excerpt}... ({$csv_length}).");

        $solv_events = $this->solvEventParser->parse_solv_events_csv($csv);

        $solv_event_count = count($solv_events);
        $this->logger->info("Parsed {$solv_event_count} events out of CSV.");

        $this->importSolvEventsForYear($solv_events, $year);
    }

    private function importSolvEventsForYear($solv_events, $year) {
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
}
