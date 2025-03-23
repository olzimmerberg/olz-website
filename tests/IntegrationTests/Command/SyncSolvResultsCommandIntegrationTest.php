<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\SyncSolvResultsCommand;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Fetchers\SolvFetcher;
use Olz\Tests\Fake\FakeSolvFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeSyncSolvResultsCommandIntegrationTestSolvFetcher extends FakeSolvFetcher {
    public function fetchYearlyResultsJson(int|string $year): ?string {
        return $this->getMockedResponse(
            "solv_yearly_results_{$year}",
            __DIR__,
            function () use ($year) {
                $real_fetcher = new SolvFetcher();
                return $real_fetcher->fetchYearlyResultsJson($year);
            }
        ) ?: null;
    }

    public function fetchEventResultsHtml(int|string $rank_id): ?string {
        return $this->getMockedResponse(
            "solv_results_html_{$rank_id}",
            __DIR__,
            function () use ($rank_id) {
                $real_fetcher = new SolvFetcher();
                return $real_fetcher->fetchEventResultsHtml($rank_id);
            }
        ) ?: null;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvResultsCommand
 */
final class SyncSolvResultsCommandIntegrationTest extends IntegrationTestCase {
    public function testRun(): void {
        $this->withLockedDb(function () {
            $job = $this->getSut();
            WithUtilsCache::set('solvFetcher', new FakeSyncSolvResultsCommandIntegrationTestSolvFetcher());
            $input = new ArrayInput(['year' => '2020']);
            $output = new BufferedOutput();

            $result = $job->run($input, $output);

            // Way too big to test.
            $this->assertSame([
                'INFO Running command Olz\Command\SyncSolvResultsCommand...',
                'INFO Syncing SOLV results for 2020...',
                <<<'ZZZZZZZZZZ'
                    INFO Successfully read JSON: {
                      "ResultLists" : [
                         {
                          "UniqueID"  : 10241,
                          "EventDate" : "2020-08-15",
                          "EventName" : "Zimmerberg Berg-OL",
                          "EventCity" : "",
                          "EventMap"  : "Mullern ob Mollis (GL)",
                          "EventClub" : "OL Zimmerberg",
                          "EventType... (1154).
                    ZZZZZZZZZZ,
                'INFO Parsed JSON: 3 results.',
                'INFO SOLV events from DB: 1 results.',
                'INFO Event with SOLV ID 10241 has new results.',
                'INFO Number of results fetched & parsed: 78',
                'INFO Event with SOLV ID 10563 has new results.',
                'INFO Number of results fetched & parsed: 1',
                'INFO Event with SOLV ID 10317 has new results.',
                'INFO Number of results fetched & parsed: 43',
                'INFO Successfully ran command Olz\Command\SyncSolvResultsCommand.',
            ], $this->getLogs());

            $this->assertSame(Command::SUCCESS, $result);

            $entity_manager = $this->getEntityManager();
            $solv_person_repo = $entity_manager->getRepository(SolvPerson::class);
            $all_persons = $solv_person_repo->findAll();
            $solv_result_repo = $entity_manager->getRepository(SolvResult::class);
            $all_results = $solv_result_repo->findAll();

            $this->assertSame([
                'Toni 😁 Thalwiler',
                'Hanna Horgener',
                'Walter Wädenswiler',
                'Regula Richterswiler',
            ], array_map(function ($person) {
                return $person->getName();
            }, $all_persons));

            $this->assertSame([
                '1 6822 HAL 1234 12',
                '2 6822 DAM 4321 43',
                '3 6822 HAK 4231 32',
                '1 7411 HAL 1234 12',
                '3 7411 HAK 4231 32',
                '4 7411 DAK 4321 43',
                '0 10241 Gruen 1189 29',
                '0 10241 Gruen 1310 27',
                '0 10241 Gruen 1623 37',
                '0 10241 Gruen 1641 52',
                '0 10241 Gruen 1826 30',
                '0 10241 Gruen 1911 40',
                '0 10241 Gruen 2148 52',
                '0 10241 Gruen 2375 38',
                '0 10241 Gruen 2443 39',
                '0 10241 Gruen 2536 41',
                '0 10241 Gruen 2581 53',
                '0 10241 Gruen 2655 56',
                '0 10241 Gruen 3122 48',
                '0 10241 Gruen 3152 59',
                '0 10241 Gruen 4265 1824',
                '0 10241 Blau 2758 69',
                '0 10241 Blau 3070 43',
                '0 10241 Blau 3443 36',
                '0 10241 Hellrot 2378 32',
                '0 10241 Hellrot 2518 45',
                '0 10241 Hellrot 2652 34',
                '0 10241 Hellrot 2737 35',
                '0 10241 Hellrot 2836 37',
                '0 10241 Hellrot 2861 37',
                '0 10241 Hellrot 2917 46',
                '0 10241 Hellrot 3087 33',
                '0 10241 Hellrot 3107 36',
                '0 10241 Hellrot 3160 38',
                '0 10241 Hellrot 3251 28',
                '0 10241 Hellrot 3282 48',
                '0 10241 Hellrot 3307 36',
                '0 10241 Hellrot 3370 27',
                '0 10241 Hellrot 3399 31',
                '0 10241 Hellrot 3430 42',
                '0 10241 Hellrot 3482 64',
                '0 10241 Hellrot 3505 34',
                '0 10241 Hellrot 3505 58',
                '0 10241 Hellrot 3556 36',
                '0 10241 Hellrot 3593 35',
                '0 10241 Hellrot 3613 49',
                '0 10241 Hellrot 3803 41',
                '0 10241 Hellrot 3819 48',
                '0 10241 Hellrot 3826 36',
                '0 10241 Hellrot 4082 51',
                '0 10241 Hellrot 4289 37',
                '0 10241 Hellrot 4435 26',
                '0 10241 Hellrot 4647 53',
                '0 10241 Hellrot 5164 41',
                '0 10241 Hellrot 5262 72',
                '0 10241 Hellrot 5507 62',
                '0 10241 Hellrot 6664 44',
                '0 10241 Hellrot -1 36',
                '0 10241 Hellrot -1 41',
                '0 10241 Schwarz 3559 27',
                '0 10241 Schwarz 4107 33',
                '0 10241 Schwarz 4324 32',
                '0 10241 Schwarz 4351 27',
                '0 10241 Schwarz 4530 28',
                '0 10241 Schwarz 4591 20',
                '0 10241 Schwarz 4873 22',
                '0 10241 Schwarz 5011 27',
                '0 10241 Schwarz 5039 23',
                '0 10241 Schwarz 5316 27',
                '0 10241 Schwarz 5320 20',
                '0 10241 Schwarz 5369 32',
                '0 10241 Schwarz 6205 24',
                '0 10241 Schwarz 6332 27',
                '0 10241 Schwarz 6338 26',
                '0 10241 Schwarz 6423 43',
                '0 10241 Schwarz 6445 42',
                '0 10241 Schwarz 6554 39',
                '0 10241 Schwarz 6785 49',
                '0 10241 Schwarz 7253 34',
                '0 10241 Schwarz 7313 25',
                '0 10241 Schwarz -1 23',
                '0 10241 Schwarz -1 34',
                '0 10241 Schwarz -1 87',
                '0 10241 Schwarz -1 73',
                '0 10563 C 3416 20',
                '0 10317 HAL 6427 23',
                '0 10317 HAM -1 35',
                '0 10317 H55 5001 35',
                '0 10317 H60 5512 34',
                '0 10317 H18 4198 21',
                '0 10317 H16 3272 23',
                '0 10317 H16 4736 21',
                '0 10317 H14 1707 20',
                '0 10317 H14 2059 23',
                '0 10317 H14 2319 25',
                '0 10317 H14 2422 25',
                '0 10317 H14 2986 27',
                '0 10317 H14 3682 24',
                '0 10317 H14 5191 26',
                '0 10317 H12 1202 23',
                '0 10317 H12 1421 28',
                '0 10317 H12 1782 43',
                '0 10317 H12 2327 27',
                '0 10317 H12 2558 31',
                '0 10317 H10 1121 26',
                '0 10317 H10 2896 33',
                '0 10317 DAM 4196 28',
                '0 10317 DAK 4078 35',
                '0 10317 DAK 4435 48',
                '0 10317 DB 4150 33',
                '0 10317 D60 3368 29',
                '0 10317 D18 3971 25',
                '0 10317 D18 4134 25',
                '0 10317 D16 3197 25',
                '0 10317 D14 2357 22',
                '0 10317 D14 2425 27',
                '0 10317 D12 1455 22',
                '0 10317 D12 4023 27',
                '0 10317 OL 4910 37',
                '0 10317 OK 1756 30',
                '0 10317 OK 1770 30',
                '0 10317 OK 2440 48',
                '0 10317 OS 2589 36',
                '0 10317 OS 3785 43',
                '0 10317 FAM 1831 24',
                '0 10317 FAM 2780 75',
                '0 10317 FAM 2884 41',
                '0 10317 FAM 3433 56',
            ], array_map(function ($result) {
                $person = $result->getPerson();
                $event = $result->getEvent();
                $class = $result->getClass();
                $time = $result->getResult();
                $finish_split = $result->getFinishSplit();
                return "{$person} {$event} {$class} {$time} {$finish_split}";
            }, $all_results));
        });
    }

    protected function getSut(): SyncSolvResultsCommand {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SyncSolvResultsCommand::class);
    }

    protected function getEntityManager(): EntityManagerInterface {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
