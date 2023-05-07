<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SyncSolvCommand;

use Olz\Command\SyncSolvCommand\SolvResultsSyncer;
use Olz\Entity\SolvEvent;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeSolvResultsSyncerSolvEventRepository {
    public $eventWithResults;
    public $eventWithoutResults;
    public $updatedRankIdBySolvUid = [];

    public function __construct() {
        $event_with_results = Fake\FakeSolvEvent::defaultSolvEvent(true);
        $event_with_results->setSolvUid(20202);
        $event_with_results->setName('Event with results');
        $event_with_results->setLastModification('2020-01-11 21:48:36');
        $event_with_results->setRankLink(1235);
        $this->eventWithResults = $event_with_results;

        $event_without_results = Fake\FakeSolvEvent::defaultSolvEvent(true);
        $event_without_results->setSolvUid(20201);
        $event_without_results->setName('Event without results');
        $event_without_results->setLastModification('2020-01-11 21:36:48');
        $this->eventWithoutResults = $event_without_results;
    }

    public function getSolvEventsForYear($year) {
        switch ($year) {
            case '2020':
                return [
                    $this->eventWithResults,
                    $this->eventWithoutResults,
                ];
            default:
                return [];
        }
    }

    public function setResultForSolvEvent($solv_uid, $rank_id) {
        $this->updatedRankIdBySolvUid[$solv_uid] = $rank_id;
    }
}

class FakeSolvResultsSyncerSolvFetcher {
    public function fetchYearlyResultsJson($year) {
        switch ($year) {
            case '2020':
                return json_encode(['ResultLists' => [
                    [
                        'UniqueID' => 20201,
                        'EventDate' => '2020-04-01',
                        'EventName' => 'Inserted Event',
                        'EventCity' => 'Dübendorf',
                        'EventMap' => 'Dübendorf',
                        'EventClub' => 'OLC Kapreolo',
                        'EventType' => 'reg',
                        'SubTitle' => 'Sprint',
                        'ResultListID' => 1234,
                        'ResultType' => 0,
                        'ResultModTime' => '20200313T112538',
                    ],
                    [
                        'UniqueID' => 20202,
                        'EventDate' => '2020-04-02',
                        'EventName' => 'Modified Event (after)',
                        'EventCity' => 'Albispass',
                        'EventMap' => 'Sihlwald',
                        'EventClub' => 'OL Zimmerberg',
                        'EventType' => 'reg',
                        'SubTitle' => 'Sprint',
                        'ResultListID' => 1235,
                        'ResultType' => 0,
                        'ResultModTime' => '20200313T105538',
                    ],
                ]]);
            default:
                return "{}";
        }
    }

    public function fetchEventResultsHtml($rank_id) {
        switch ($rank_id) {
            case 1234:
                return
                    "<b><p></p><a href=\"results?type=rang&year=2020&rl_id=1234&kat=H10&zwizt=1\">H10</a></b>\n".
                    "<pre>( 1.6 km,  10 m, 17 Po.)  14 Teilnehmer\n".
                    "\n".
                    "<b>  1. Thurgauer Gewinner     10  Thundorf           thurgorienta           9:14</b>\n".
                    "    1.   0.38 (2)  2.   1.07 (3)  3.   1.34 (2)  4.   1.35 (2)  5.   1.54 (2)\n".
                    "   131   0.38 (2) 164   0.29 (3) 140   0.27 (1) 145   0.01 (1) 180   0.19 (1)\n".
                    "         0.04           0.05           0.00           0.00           0.00\n".
                    "    6.   2.30 (2)  7.   2.40 (2)  8.   3.17 (1)  9.   3.18 (1) 10.   3.53 (1)\n".
                    "   185   0.36 (2)  95   0.10 (1) 160   0.37 (1) 165   0.01 (1)  92   0.35 (1)\n".
                    "         0.03           0.00           0.00           0.00           0.00\n".
                    "   11.   4.29 (1) 12.   5.05 (1) 13.   6.04 (1) 14.   6.26 (1) 15.   7.30 (1)\n".
                    "   170   0.36 (1)  96   0.36 (6)  94   0.59 (2)  93   0.22 (4)  97   1.04 (2)\n".
                    "         0.00           0.08           0.03           0.05           0.06\n".
                    "   16.   8.27 (1) 17.   8.48 (1)       9.14 (1)\n".
                    "   177   0.57 (4) 190   0.21 (1)  Zi   0.26 (2)\n".
                    "         0.06           0.00           0.01\n".
                    "<b>  4. Martin Tester          10  Richterswil        OL Zimmerberg         11:04</b>\n".
                    "    1.   0.38 (2)  2.   1.06 (2)  3.   1.40 (3)  4.   1.41 (3)  5.   2.10 (4)\n".
                    "   131   0.38 (2) 164   0.28 (2) 140   0.34 (6) 145   0.01 (1) 180   0.29 (7)\n".
                    "         0.04           0.04           0.07           0.00           0.10\n".
                    "    6.   3.15 (5)  7.   3.31 (5)  8.   4.22 (5)  9.   4.25 (5) 10.   5.02 (4)\n".
                    "   185   1.05(10)  95   0.16 (4) 160   0.51 (5) 165   0.03(10)  92   0.37 (2)\n".
                    "         0.32           0.06           0.14           0.02           0.02\n".
                    "   11.   5.41 (4) 12.   6.14 (4) 13.   7.38 (4) 14.   7.55 (4) 15.   9.14 (4)\n".
                    "   170   0.39 (2)  96   0.33 (5)  94   1.24 (7)  93   0.17 (1)  97   1.19 (4)\n".
                    "         0.03           0.05           0.28           0.00           0.21\n".
                    "   16.  10.07 (4) 17.  10.33 (4)      11.04 (4)\n".
                    "   177   0.53 (2) 190   0.26 (3)  Zi   0.31(10)\n".
                    "         0.02           0.05           0.06\n".
                    "</pre>\n".
                    "<b><a href=\"results?type=rang&year=2020&rl_id=1234&kat=H12&zwizt=1\">H12</a></b>\n".
                    "<pre>( 1.9 km,  10 m, 20 Po.)  18 Teilnehmer\n".
                    "\n".
                    "<b>  1. Unser Gewinner         08  Oberrieden         OL Zimmerberg         11:42</b>\n".
                    "    1.   0.38 (5)  2.   0.59 (4)  3.   1.36 (3)  4.   2.03 (3)  5.   2.04 (3)\n".
                    "   131   0.38 (5) 134   0.21 (6) 164   0.37 (4) 140   0.27 (4) 145   0.01 (1)\n".
                    "         0.09           0.07           0.04           0.08           0.00\n".
                    "    6.   2.25 (3)  7.   2.42 (3)  8.   3.06 (3)  9.   3.19 (3) 10.   4.03 (3)\n".
                    "   180   0.21 (5) 146   0.17(11) 185   0.24 (3)  95   0.13 (6) 160   0.44 (7)\n".
                    "         0.07           0.06           0.04           0.05           0.07\n".
                    "   11.   4.04 (3) 12.   4.43 (3) 13.   5.03 (3) 14.   5.41 (3) 15.   6.38 (3)\n".
                    "   165   0.01 (1)  92   0.39 (9)  91   0.20(15) 170   0.38 (1) 139   0.57(11)\n".
                    "         0.00           0.14           0.11           0.00           0.16\n".
                    "   16.   8.06 (3) 17.   8.56 (3) 18.   9.57 (3) 19.  10.42 (3) 20.  11.10 (3)\n".
                    "    94   1.28(10)  96   0.50 (3) 176   1.01(14) 177   0.45 (9) 190   0.28(10)\n".
                    "         0.25           0.06           0.23           0.10           0.08\n".
                    "        11.42 (3)\n".
                    "    Zi   0.32(13)\n".
                    "         0.08\n".
                    "<b>  9. Toni Pfister           09  Horgen             OL Zimmerberg         13:45</b>\n".
                    "    1.   0.51(13)  2.   1.31(11)  3.   2.14(10)  4.   2.43 (9)  5.   2.44 (8)\n".
                    "   131   0.51(13) 134   0.40(15) 164   0.43 (6) 140   0.29 (8) 145   0.01 (1)\n".
                    "         0.22           0.26           0.10           0.10           0.00\n".
                    "    6.   3.49(14)  7.   4.03(13)  8.   4.44(10)  9.   4.59(11) 10.   5.48 (9)\n".
                    "   180   1.05(17) 146   0.14 (6) 185   0.41(10)  95   0.15 (9) 160   0.49 (9)\n".
                    "         0.51           0.03           0.21           0.07           0.12\n".
                    "   11.   5.49 (9) 12.   6.24 (8) 13.   6.40 (8) 14.   7.18 (8) 15.   9.10(12)\n".
                    "   165   0.01 (1)  92   0.35 (3)  91   0.16(10) 170   0.38 (1) 139   1.52(16)\n".
                    "         0.00           0.10           0.07           0.00           1.11\n".
                    "   16.  10.23(10) 17.  11.15(10) 18.  12.04 (9) 19.  12.44 (9) 20.  13.20 (9)\n".
                    "    94   1.13 (3)  96   0.52 (5) 176   0.49 (8) 177   0.40 (4) 190   0.36(15)\n".
                    "         0.10           0.08           0.11           0.05           0.16\n".
                    "        13.45 (9)\n".
                    "    Zi   0.25 (2)\n".
                    "         0.01\n".
                    "</pre>";
            default:
                return "";
        }
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvCommand\SolvResultsSyncer
 */
final class SolvResultsSyncerTest extends UnitTestCase {
    public function testSolvResultsSyncer(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $solv_event_repo = new FakeSolvResultsSyncerSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $solv_fetcher = new FakeSolvResultsSyncerSolvFetcher();

        $job = new SolvResultsSyncer();
        $job->setSolvFetcher($solv_fetcher);

        $job->syncSolvResultsForYear('2020');

        $flushed = $entity_manager->flushed_persisted;
        $this->assertSame(3, count($flushed));

        $solv_event_repo = $entity_manager->getRepository(SolvEvent::class);
        $this->assertSame([20201 => 1234], $solv_event_repo->updatedRankIdBySolvUid);
        $this->assertSame('Martin Tester', $flushed[0]->getName());
        $this->assertSame('Unser Gewinner', $flushed[1]->getName());
        $this->assertSame('Toni Pfister', $flushed[2]->getName());
    }
}
