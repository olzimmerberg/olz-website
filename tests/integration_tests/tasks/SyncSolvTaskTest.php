<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../src/tasks/SyncSolvTask.php';

class FakeEntityManager {
    private $persisted = [];
    private $flushed = [];

    public function getRepository($class) {
        switch ($class) {
            case 'SolvEvent':
                return new FakeSolvEventRepository();
            case 'SolvPerson':
                return new FakeSolvPersonRepository();
            case 'SolvResult':
                return new FakeSolvResultRepository();
            default:
                break;
        }
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }

    public function getFlushed() {
        return $this->flushed;
    }
}

class FakeSolvEventRepository {
    public function getSolvEventsForYear($year) {
        return [];
    }
}

class FakeSolvPersonRepository {
    public function getSolvPersonsMarkedForMerge() {
        return [];
    }
}

class FakeSolvResultRepository {
    public function getUnassignedSolvResults() {
        return [];
    }
}

class FakeSolvFetcher {
    public function fetchEventsCsvForYear($year) {
        return
            "unique_id;date;duration;kind;day_night;national;region;type;event_name;event_link;club;map;location;coord_x;coord_y;deadline;entryportal;last_modification\n".
            "1234;2020-01-01;1;foot;day;0;ZH/SH;*1;Winter Stadt OL;https://kapreolo.ch/de/;OLC Kapreolo;Dübendorf;Dübendorf;689225;250900;;2;2019-12-05 00:18:59"
        ;
    }

    public function fetchYearlyResultsJson($year) {
        return "{}";
    }
}

/**
 * @internal
 * @coversNothing
 */
final class SyncSolvTaskTest extends TestCase {
    public function __construct() {
        parent::__construct();
        $this->solv_fetcher = new SolvFetcher();
    }

    public function testSyncSolvTask(): void {
        $entity_manager = new FakeEntityManager();
        $solv_fetcher = new FakeSolvFetcher();
        $logger = new Logger('SyncSolvTaskTest');

        $job = new SyncSolvTask($entity_manager, $solv_fetcher);
        $job->setLogger($logger);
        $job->run();

        $this->assertSame(4, count($entity_manager->getFlushed()));
    }
}
