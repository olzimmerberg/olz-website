<?php

declare(strict_types=1);

use Monolog\Logger;
use Olz\Entity\SolvEvent;
use Olz\Tasks\SyncSolvTask\SolvEventsSyncer;

require_once __DIR__.'/../../../fake/fake_solv_event.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeSolvEventsSyncerSolvEventRepository {
    public function __construct() {
        $modified_event = get_fake_solv_event();
        $modified_event->setSolvUid(20202);
        $modified_event->setName('Modified Event (before)');
        $modified_event->setLastModification('2020-01-11 21:48:36');
        $modified_event->setRankLink(1235);
        $this->modifiedEvent = $modified_event;

        $deleted_event = get_fake_solv_event();
        $deleted_event->setSolvUid(20203);
        $deleted_event->setName('Deleted Event');
        $deleted_event->setLastModification('2020-01-11 21:36:48');
        $this->deletedEvent = $deleted_event;
    }

    public function getSolvEventsForYear($year) {
        switch ($year) {
            case '2020':
                return [
                    $this->modifiedEvent,
                    $this->deletedEvent,
                ];
            default:
                return [];
        }
    }

    public function deleteBySolvUid($solv_uid) {
        $this->deletedSolvUids[] = $solv_uid;
    }
}

class FakeSolvEventsSyncerSolvFetcher {
    public function fetchEventsCsvForYear($year) {
        switch ($year) {
            case '2020':
                return
                    "unique_id;date;duration;kind;day_night;national;region;type;event_name;event_link;club;map;location;coord_x;coord_y;deadline;entryportal;last_modification\n".
                    "20201;2020-04-01;1;foot;day;0;ZH/SH;*1;Inserted Event;http://test.olzimmerberg.ch;OLC Kapreolo;Dübendorf;Dübendorf;689225;250900;;2;2020-03-13 09:13:27\n".
                    "20202;2020-04-02;1;foot;day;0;ZH/SH;*1;Modified Event (after);;OL Zimmerberg;Sihlwald;Albispass;681240;237075;;2;2020-03-13 13:09:27"
                ;
            default:
                return "unique_id;date;duration;kind;day_night;national;region;type;event_name;event_link;club;map;location;coord_x;coord_y;deadline;entryportal;last_modification\n";
        }
    }
}

/**
 * @internal
 * @covers \Olz\Tasks\SyncSolvTask\SolvEventsSyncer
 */
final class SolvEventsSyncerTest extends UnitTestCase {
    public function testSolvEventsSyncer(): void {
        $entity_manager = new FakeEntityManager();
        $solv_event_repo = new FakeSolvEventsSyncerSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $solv_fetcher = new FakeSolvEventsSyncerSolvFetcher();
        $logger = new Logger('SolvEventsSyncerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvEventsSyncer($entity_manager, $solv_fetcher);
        $job->setLogger($logger);
        $job->syncSolvEventsForYear('2020');

        $flushed = $entity_manager->flushed_persisted;
        $this->assertSame(1, count($flushed));
        $this->assertSame('20201', $flushed[0]->getSolvUid());
        $this->assertSame('Inserted Event', $flushed[0]->getName());
        $this->assertSame('2020-03-13 09:13:27', $flushed[0]->getLastModification()->format('Y-m-d H:i:s'));
        $solv_event_repo = $entity_manager->getRepository(SolvEvent::class);
        $modified_event = $solv_event_repo->modifiedEvent;
        $this->assertSame(20202, $modified_event->getSolvUid());
        $this->assertSame('Modified Event (after)', $modified_event->getName());
        $this->assertSame('2020-03-13 13:09:27', $modified_event->getLastModification()->format('Y-m-d H:i:s'));
        $this->assertSame([20203], $solv_event_repo->deletedSolvUids);
    }
}
