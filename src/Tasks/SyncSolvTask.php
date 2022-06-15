<?php

namespace Olz\Tasks;

use Olz\Tasks\Common\BackgroundTask;
use Olz\Tasks\SyncSolvTask\SolvEventsSyncer;
use Olz\Tasks\SyncSolvTask\SolvPeopleAssigner;
use Olz\Tasks\SyncSolvTask\SolvPeopleMerger;
use Olz\Tasks\SyncSolvTask\SolvResultsSyncer;

$solv_maintainer_email = 'simon.hatt@olzimmerberg.ch';

class SyncSolvTask extends BackgroundTask {
    public function __construct($entityManager, $solvFetcher, $dateUtils, $envUtils) {
        parent::__construct($dateUtils, $envUtils);
        $this->entityManager = $entityManager;
        $this->solvFetcher = $solvFetcher;
        $this->solvEventsSyncer = new SolvEventsSyncer($entityManager, $solvFetcher, $this->logger);
        $this->solvResultsSyncer = new SolvResultsSyncer($entityManager, $solvFetcher, $this->logger);
        $this->solvPeopleAssigner = new SolvPeopleAssigner($entityManager, $this->logger);
        $this->solvPeopleMerger = new SolvPeopleMerger($entityManager, $this->logger);
    }

    public function setSolvEventsSyncer($new_solv_events_syncer) {
        $this->solvEventsSyncer = $new_solv_events_syncer;
    }

    public function setSolvResultsSyncer($new_solv_results_syncer) {
        $this->solvResultsSyncer = $new_solv_results_syncer;
    }

    public function setSolvPeopleAssigner($new_solv_people_assigner) {
        $this->solvPeopleAssigner = $new_solv_people_assigner;
    }

    public function setSolvPeopleMerger($new_solv_people_merger) {
        $this->solvPeopleMerger = $new_solv_people_merger;
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
        $current_year = intval($this->dateUtils->getCurrentDateInFormat('Y'));
        $events_syncer = $this->solvEventsSyncer;
        $events_syncer->setLogger($this->logger);
        $events_syncer->syncSolvEventsForYear($current_year);
        $events_syncer->syncSolvEventsForYear($current_year - 1);
        $events_syncer->syncSolvEventsForYear($current_year + 1);
        $events_syncer->syncSolvEventsForYear($current_year - 2);
    }

    private function syncSolvResults() {
        $current_year = intval($this->dateUtils->getCurrentDateInFormat('Y'));
        $results_syncer = $this->solvResultsSyncer;
        $results_syncer->setLogger($this->logger);
        $results_syncer->syncSolvResultsForYear($current_year);
    }

    private function assignSolvPeople() {
        $people_assigner = $this->solvPeopleAssigner;
        $people_assigner->setLogger($this->logger);
        $people_assigner->assignSolvPeople();
    }

    private function mergeSolvPeople() {
        $people_merger = $this->solvPeopleMerger;
        $people_merger->setLogger($this->logger);
        $people_merger->mergeSolvPeople();
    }
}
