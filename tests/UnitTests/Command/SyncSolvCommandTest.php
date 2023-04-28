<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SyncSolvCommand;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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
 * @covers \Olz\Command\SyncSolvCommand
 */
final class SyncSolvCommandTest extends UnitTestCase {
    public function testSyncSolvCommand(): void {
        $entity_manager = null;
        $solv_fetcher = null;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $solv_events_syncer = new FakeSolvEventsSyncer();
        $solv_results_syncer = new FakeSolvResultsSyncer();
        $solv_people_assigner = new FakeSolvPeopleAssigner();
        $solv_people_merger = new FakeSolvPeopleMerger();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SyncSolvCommand();
        $job->setDateUtils($date_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setSolvFetcher($solv_fetcher);
        $job->setLog($logger);
        $job->setSolvEventsSyncer($solv_events_syncer);
        $job->setSolvResultsSyncer($solv_results_syncer);
        $job->setSolvPeopleAssigner($solv_people_assigner);
        $job->setSolvPeopleMerger($solv_people_merger);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncSolvCommand...',
            'INFO Successfully ran command Olz\Command\SyncSolvCommand.',
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([2020], $solv_events_syncer->years_synced);
        $this->assertSame([2020], $solv_results_syncer->years_synced);
        $this->assertSame(true, $solv_people_assigner->people_assigned);
        $this->assertSame(true, $solv_people_merger->people_merged);
    }

    public function testSyncSolvCommandFirstOfMonth(): void {
        $entity_manager = null;
        $solv_fetcher = null;
        $date_utils = new FixedDateUtils('2020-04-01 19:30:00');
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $solv_events_syncer = new FakeSolvEventsSyncer();
        $solv_results_syncer = new FakeSolvResultsSyncer();
        $solv_people_assigner = new FakeSolvPeopleAssigner();
        $solv_people_merger = new FakeSolvPeopleMerger();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SyncSolvCommand();
        $job->setDateUtils($date_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setSolvFetcher($solv_fetcher);
        $job->setLog($logger);
        $job->setSolvEventsSyncer($solv_events_syncer);
        $job->setSolvResultsSyncer($solv_results_syncer);
        $job->setSolvPeopleAssigner($solv_people_assigner);
        $job->setSolvPeopleMerger($solv_people_merger);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncSolvCommand...',
            'INFO Successfully ran command Olz\Command\SyncSolvCommand.',
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([2020, 2019, 2021, 2018], $solv_events_syncer->years_synced);
        $this->assertSame([2020], $solv_results_syncer->years_synced);
        $this->assertSame(true, $solv_people_assigner->people_assigned);
        $this->assertSame(true, $solv_people_merger->people_merged);
    }
}
