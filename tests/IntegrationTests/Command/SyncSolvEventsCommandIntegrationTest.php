<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\SyncSolvEventsCommand;
use Olz\Entity\SolvEvent;
use Olz\Fetchers\SolvFetcher;
use Olz\Tests\Fake\FakeSolvFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeSyncSolvEventsCommandIntegrationTestSolvFetcher extends FakeSolvFetcher {
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
}

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvEventsCommand
 */
final class SyncSolvEventsCommandIntegrationTest extends IntegrationTestCase {
    public function testRun(): void {
        $this->withLockedDb(function () {
            $job = $this->getSut();
            WithUtilsCache::set('solvFetcher', new FakeSyncSolvEventsCommandIntegrationTestSolvFetcher());
            $input = new ArrayInput(['year' => '2020']);
            $output = new BufferedOutput();

            $result = $job->run($input, $output);

            $this->assertSame([
                'INFO Running command Olz\Command\SyncSolvEventsCommand...',
                'INFO Syncing SOLV events for 2020...',
                "INFO Successfully read CSV: unique_id;date;duration;kind;day_night;national;region;type;event_name;event_link;club;map;location;coord_x;coord_y;deadline;entryportal;last_modification\n10241;2020-08-15;1;foot;day;0;GL/GR;142;Zimmerberg Berg-OL;https://olzimmerberg.ch/files/aktuell/504... (739).",
                'INFO Parsed 3 events out of CSV.',
                'INFO INSERTED 10241',
                'INFO INSERTED 10563',
                'INFO INSERTED 10317',
                'INFO DELETED 12345',
                'INFO Successfully ran command Olz\Command\SyncSolvEventsCommand.',
            ], $this->getLogs());

            $this->assertSame(Command::SUCCESS, $result);

            $entity_manager = $this->getEntityManager();
            $solv_event_repo = $entity_manager->getRepository(SolvEvent::class);
            $all_events = $solv_event_repo->findAll();

            $this->assertSame([
                '6. Nationaler OL 🥶',
                '59. Schweizer 5er Staffel',
                'Zimmerberg Berg-OL',
                '13. Zimmerberg-OL / JOM-Schlusslauf',
                '4. Milchsuppe-Abend OL 2020',
            ], array_map(function ($event) {
                return $event->getName();
            }, $all_events));
        });
    }

    protected function getSut(): SyncSolvEventsCommand {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SyncSolvEventsCommand::class);
    }

    protected function getEntityManager(): EntityManagerInterface {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
