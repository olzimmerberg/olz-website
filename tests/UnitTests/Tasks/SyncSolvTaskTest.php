<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks;

use Olz\Tasks\SyncSolvTask;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../Fake/fake_solv_event.php';

class FakeSolvEventsSyncer {
    use \Psr\Log\LoggerAwareTrait;

    public $years_synced = [];

    public function syncSolvEventsForYear($year) {
        $this->years_synced[] = $year;
    }
}

class FakeSolvResultsSyncer {
    use \Psr\Log\LoggerAwareTrait;

    public $years_synced = [];

    public function syncSolvResultsForYear($year) {
        $this->years_synced[] = $year;
    }
}

class FakeSolvPeopleAssigner {
    use \Psr\Log\LoggerAwareTrait;

    public $people_assigned = false;

    public function assignSolvPeople() {
        $this->people_assigned = true;
    }
}

class FakeSolvPeopleMerger {
    use \Psr\Log\LoggerAwareTrait;

    public $people_merged = false;

    public function mergeSolvPeople() {
        $this->people_merged = true;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Tasks\SyncSolvTask
 */
final class SyncSolvTaskTest extends UnitTestCase {
    public function testSyncSolvTask(): void {
        $entity_manager = null;
        $solv_fetcher = null;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $solv_events_syncer = new FakeSolvEventsSyncer();
        $solv_results_syncer = new FakeSolvResultsSyncer();
        $solv_people_assigner = new FakeSolvPeopleAssigner();
        $solv_people_merger = new FakeSolvPeopleMerger();

        $job = new SyncSolvTask();
        $job->setDateUtils($date_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setSolvFetcher($solv_fetcher);
        $job->setLog($logger);
        $job->setSolvEventsSyncer($solv_events_syncer);
        $job->setSolvResultsSyncer($solv_results_syncer);
        $job->setSolvPeopleAssigner($solv_people_assigner);
        $job->setSolvPeopleMerger($solv_people_merger);
        $job->run();

        $this->assertSame([
            'INFO Setup task SyncSolv...',
            'INFO Running task SyncSolv...',
            'INFO Finished task SyncSolv.',
            'INFO Teardown task SyncSolv...',
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([2020, 2019, 2021, 2018], $solv_events_syncer->years_synced);
        $this->assertSame([2020], $solv_results_syncer->years_synced);
        $this->assertSame(true, $solv_people_assigner->people_assigned);
        $this->assertSame(true, $solv_people_merger->people_merged);
    }
}
