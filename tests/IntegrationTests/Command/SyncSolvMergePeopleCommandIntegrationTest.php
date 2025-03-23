<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\SyncSolvMergePeopleCommand;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Fetchers\SolvFetcher;
use Olz\Tests\Fake\FakeSolvFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeSyncSolvMergePeopleCommandIntegrationTestSolvFetcher extends FakeSolvFetcher {
    public function fetchEventsCsvForYear(int|string $year): ?string {
        return $this->getMockedResponse(
            "solv_events_{$year}",
            __DIR__,
            function () use ($year) {
                $real_fetcher = new SolvFetcher();
                return $real_fetcher->fetchEventsCsvForYear($year);
            }
        ) ?: null;
    }

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
 * @covers \Olz\Command\SyncSolvMergePeopleCommand
 */
final class SyncSolvMergePeopleCommandIntegrationTest extends IntegrationTestCase {
    public function testRun(): void {
        $this->withLockedDb(function () {
            WithUtilsCache::set('solvFetcher', new FakeSyncSolvMergePeopleCommandIntegrationTestSolvFetcher());
            $job = $this->getSut();
            $input = new ArrayInput([]);
            $output = new BufferedOutput();

            $result = $job->run($input, $output);

            $this->assertSame([
                'INFO Running command Olz\Command\SyncSolvMergePeopleCommand...',
                'INFO Successfully ran command Olz\Command\SyncSolvMergePeopleCommand.',
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

    protected function getSut(): SyncSolvMergePeopleCommand {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SyncSolvMergePeopleCommand::class);
    }

    protected function getEntityManager(): EntityManagerInterface {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
