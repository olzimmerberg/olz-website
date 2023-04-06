<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Command\SyncSolvCommand\SolvEventsSyncer;
use Olz\Command\SyncSolvCommand\SolvPeopleAssigner;
use Olz\Command\SyncSolvCommand\SolvPeopleMerger;
use Olz\Command\SyncSolvCommand\SolvResultsSyncer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$solv_maintainer_email = 'simon.hatt@olzimmerberg.ch';

#[AsCommand(name: 'olz:syncSolv')]
class SyncSolvCommand extends OlzCommand {
    protected $solvFetcher;
    protected $solvEventsSyncer;
    protected $solvResultsSyncer;
    protected $solvPeopleAssigner;
    protected $solvPeopleMerger;

    public function setSolvFetcher($solvFetcher) {
        $this->solvFetcher = $solvFetcher;
    }

    public function setSolvEventsSyncer($solvEventsSyncer) {
        $this->solvEventsSyncer = $solvEventsSyncer;
    }

    public function setSolvResultsSyncer($solvResultsSyncer) {
        $this->solvResultsSyncer = $solvResultsSyncer;
    }

    public function setSolvPeopleAssigner($solvPeopleAssigner) {
        $this->solvPeopleAssigner = $solvPeopleAssigner;
    }

    public function setSolvPeopleMerger($solvPeopleMerger) {
        $this->solvPeopleMerger = $solvPeopleMerger;
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->syncSolvEvents();
        $this->syncSolvResults();
        $this->assignSolvPeople();
        $this->mergeSolvPeople();
        return Command::SUCCESS;
    }

    private function syncSolvEvents() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $current_day = intval($this->dateUtils()->getCurrentDateInFormat('d'));
        $events_syncer = $this->solvEventsSyncer ?? new SolvEventsSyncer(
            $this->entityManager(), $this->solvFetcher);
        $events_syncer->setLogger($this->log());
        try {
            $events_syncer->syncSolvEventsForYear($current_year);
        } catch (\Throwable $th) {
            $this->log()->warning("syncSolvEventsForYear(0) failed.", [$th]);
        }
        if ($current_day !== 1) { // Only do this once a month.
            return;
        }
        try {
            $events_syncer->syncSolvEventsForYear($current_year - 1);
        } catch (\Throwable $th) {
            $this->log()->warning("syncSolvEventsForYear(-1) failed.", [$th]);
        }
        try {
            $events_syncer->syncSolvEventsForYear($current_year + 1);
        } catch (\Throwable $th) {
            $this->log()->warning("syncSolvEventsForYear(+1) failed.", [$th]);
        }
        try {
            $events_syncer->syncSolvEventsForYear($current_year - 2);
        } catch (\Throwable $th) {
            $this->log()->warning("syncSolvEventsForYear(-2) failed.", [$th]);
        }
    }

    private function syncSolvResults() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $results_syncer = $this->solvResultsSyncer ?? new SolvResultsSyncer(
            $this->entityManager(), $this->solvFetcher);
        $results_syncer->setLogger($this->log());
        try {
            $results_syncer->syncSolvResultsForYear($current_year);
        } catch (\Throwable $th) {
            $this->log()->warning("syncSolvResultsForYear failed.");
        }
    }

    private function assignSolvPeople() {
        $people_assigner = $this->solvPeopleAssigner ?? new SolvPeopleAssigner(
            $this->entityManager());
        $people_assigner->setLogger($this->log());
        $people_assigner->assignSolvPeople();
    }

    private function mergeSolvPeople() {
        $people_merger = $this->solvPeopleMerger ?? new SolvPeopleMerger(
            $this->entityManager());
        $people_merger->setLogger($this->log());
        $people_merger->mergeSolvPeople();
    }
}
