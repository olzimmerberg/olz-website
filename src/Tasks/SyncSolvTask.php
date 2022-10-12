<?php

namespace Olz\Tasks;

use Olz\Tasks\Common\BackgroundTask;
use Olz\Tasks\SyncSolvTask\SolvEventsSyncer;
use Olz\Tasks\SyncSolvTask\SolvPeopleAssigner;
use Olz\Tasks\SyncSolvTask\SolvPeopleMerger;
use Olz\Tasks\SyncSolvTask\SolvResultsSyncer;

$solv_maintainer_email = 'simon.hatt@olzimmerberg.ch';

class SyncSolvTask extends BackgroundTask {
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

    protected static function getIdent() {
        return "SyncSolv";
    }

    protected function runSpecificTask() {
        $this->syncSolvEvents();
        $this->syncSolvResults();
        $this->assignSolvPeople();
        $this->mergeSolvPeople();
    }

    private function syncSolvEvents() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $events_syncer = $this->solvEventsSyncer ?? new SolvEventsSyncer(
            $this->entityManager(), $this->solvFetcher);
        $events_syncer->setLogger($this->log());
        $events_syncer->syncSolvEventsForYear($current_year);
        $events_syncer->syncSolvEventsForYear($current_year - 1);
        $events_syncer->syncSolvEventsForYear($current_year + 1);
        $events_syncer->syncSolvEventsForYear($current_year - 2);
    }

    private function syncSolvResults() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $results_syncer = $this->solvResultsSyncer ?? new SolvResultsSyncer(
            $this->entityManager(), $this->solvFetcher);
        $results_syncer->setLogger($this->log());
        $results_syncer->syncSolvResultsForYear($current_year);
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
