<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnContinuouslyCommand;
use Olz\Entity\Throttling;
use Olz\Tests\Fake\Entity\FakeThrottlingRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\OnContinuouslyCommand
 */
final class OnContinuouslyCommandTest extends UnitTestCase {
    public function testOnContinuouslyCommandTooSoon(): void {
        WithUtilsCache::get('dateUtils')->testOnlySetDate('2020-03-13 02:30:00');
        $throttling_repo = $this->setUpThrottling('2020-03-13 02:29:00'); // just a minute ago
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'DEBUG Not executing daily (01:00:00) clean-temp-directory: too soon',
            'DEBUG Not executing daily (01:05:00) clean-temp-database: too soon',
            'DEBUG Not executing daily (01:10:00) clean-logs: too soon',
            'DEBUG Not executing daily (01:15:00) send-telegram-configuration: too soon',
            'DEBUG Not executing daily (01:20:00) sync-solv: too soon',
            'DEBUG Not executing daily (08:15:00) send-weekly-summary: too soon, not the right time (diff: -20700)',
            'DEBUG Not executing daily (14:30:00) send-monthly-preview: too soon, not the right time (diff: -43200)',
            'DEBUG Not executing daily (15:14:00) send-weekly-preview: too soon, not the right time (diff: 40560)',
            'DEBUG Not executing daily (16:27:00) send-deadline-warning: too soon, not the right time (diff: 36180)',
            'DEBUG Not executing daily (17:30:00) send-daily-summary: too soon, not the right time (diff: 32400)',
            'DEBUG Not executing daily (18:30:00) send-reminders: too soon, not the right time (diff: 28800)',
            'DEBUG Not executing sync-strava (every 10 minutes): too soon',
            'DEBUG Stopping workers...',
            'DEBUG Consume messages...',
            'DEBUG Ran continuously.',
            "INFO Successfully ran command Olz\\Command\\OnContinuouslyCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Running command Olz\Command\OnContinuouslyCommand...
            Running continuously...
            Continuously processing email...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 02:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandFirstOccurrence(): void {
        WithUtilsCache::get('dateUtils')->testOnlySetDate('2020-03-13 02:30:00');
        $throttling_repo = $this->setUpThrottling(false); // never occurred before
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'INFO Executing daily (01:00:00) clean-temp-directory...',
            'INFO Executing daily (01:05:00) clean-temp-database...',
            'INFO Executing daily (01:10:00) clean-logs...',
            'INFO Executing daily (01:15:00) send-telegram-configuration...',
            'INFO Executing daily (01:20:00) sync-solv...',
            'DEBUG Not executing daily (08:15:00) send-weekly-summary: not the right time (diff: -20700)',
            'DEBUG Not executing daily (14:30:00) send-monthly-preview: not the right time (diff: -43200)',
            'DEBUG Not executing daily (15:14:00) send-weekly-preview: not the right time (diff: 40560)',
            'DEBUG Not executing daily (16:27:00) send-deadline-warning: not the right time (diff: 36180)',
            'DEBUG Not executing daily (17:30:00) send-daily-summary: not the right time (diff: 32400)',
            'DEBUG Not executing daily (18:30:00) send-reminders: not the right time (diff: 28800)',
            'INFO Executing sync-strava (every 10 minutes)...',
            'DEBUG Stopping workers...',
            'DEBUG Consume messages...',
            'DEBUG Ran continuously.',
            "INFO Successfully ran command Olz\\Command\\OnContinuouslyCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Running command Olz\Command\OnContinuouslyCommand...
            Running continuously...
            Continuously processing email...
            Executing daily (01:00:00) clean-temp-directory...
            Executing daily (01:05:00) clean-temp-database...
            Executing daily (01:10:00) clean-logs...
            Executing daily (01:15:00) send-telegram-configuration...
            Executing daily (01:20:00) sync-solv...
            Executing sync-strava (every 10 minutes)...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 02:30:00'],
            ['clean-temp-directory', '2020-03-13 02:30:00'],
            ['clean-temp-database', '2020-03-13 02:30:00'],
            ['clean-logs', '2020-03-13 02:30:00'],
            ['send-telegram-configuration', '2020-03-13 02:30:00'],
            ['sync-solv', '2020-03-13 02:30:00'],
            ['sync-strava', '2020-03-13 02:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:clean-temp-directory ',
            'olz:clean-temp-database ',
            'olz:clean-logs ',
            'olz:send-telegram-configuration ',
            'olz:sync-solv ',
            'olz:sync-strava 2025',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandExecuteAllNightly(): void {
        WithUtilsCache::get('dateUtils')->testOnlySetDate('2020-03-13 02:30:00');
        $throttling_repo = $this->setUpThrottling('2020-03-12 02:30:00'); // a day ago
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'INFO Executing daily (01:00:00) clean-temp-directory...',
            'INFO Executing daily (01:05:00) clean-temp-database...',
            'INFO Executing daily (01:10:00) clean-logs...',
            'INFO Executing daily (01:15:00) send-telegram-configuration...',
            'INFO Executing daily (01:20:00) sync-solv...',
            'DEBUG Not executing daily (08:15:00) send-weekly-summary: not the right time (diff: -20700)',
            'DEBUG Not executing daily (14:30:00) send-monthly-preview: not the right time (diff: -43200)',
            'DEBUG Not executing daily (15:14:00) send-weekly-preview: not the right time (diff: 40560)',
            'DEBUG Not executing daily (16:27:00) send-deadline-warning: not the right time (diff: 36180)',
            'DEBUG Not executing daily (17:30:00) send-daily-summary: not the right time (diff: 32400)',
            'DEBUG Not executing daily (18:30:00) send-reminders: not the right time (diff: 28800)',
            'INFO Executing sync-strava (every 10 minutes)...',
            'DEBUG Stopping workers...',
            'DEBUG Consume messages...',
            'DEBUG Ran continuously.',
            "INFO Successfully ran command Olz\\Command\\OnContinuouslyCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Running command Olz\Command\OnContinuouslyCommand...
            Running continuously...
            Continuously processing email...
            Executing daily (01:00:00) clean-temp-directory...
            Executing daily (01:05:00) clean-temp-database...
            Executing daily (01:10:00) clean-logs...
            Executing daily (01:15:00) send-telegram-configuration...
            Executing daily (01:20:00) sync-solv...
            Executing sync-strava (every 10 minutes)...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 02:30:00'],
            ['clean-temp-directory', '2020-03-13 02:30:00'],
            ['clean-temp-database', '2020-03-13 02:30:00'],
            ['clean-logs', '2020-03-13 02:30:00'],
            ['send-telegram-configuration', '2020-03-13 02:30:00'],
            ['sync-solv', '2020-03-13 02:30:00'],
            ['sync-strava', '2020-03-13 02:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:clean-temp-directory ',
            'olz:clean-temp-database ',
            'olz:clean-logs ',
            'olz:send-telegram-configuration ',
            'olz:sync-solv ',
            'olz:sync-strava 2025',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandExecuteSendDaily(): void {
        $throttling_repo = $this->setUpThrottling('2020-03-11 17:30:00'); // two days ago
        $command = new OnContinuouslyCommand();
        $command->setDateUtils(new DateUtils('2020-03-13 17:30:00'));
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'DEBUG Not executing daily (01:00:00) clean-temp-directory: not the right time (diff: -27000)',
            'DEBUG Not executing daily (01:05:00) clean-temp-database: not the right time (diff: -27300)',
            'DEBUG Not executing daily (01:10:00) clean-logs: not the right time (diff: -27600)',
            'DEBUG Not executing daily (01:15:00) send-telegram-configuration: not the right time (diff: -27900)',
            'DEBUG Not executing daily (01:20:00) sync-solv: not the right time (diff: -28200)',
            'DEBUG Not executing daily (08:15:00) send-weekly-summary: not the right time (diff: 33300)',
            'DEBUG Not executing daily (14:30:00) send-monthly-preview: not the right time (diff: 10800)',
            'DEBUG Not executing daily (15:14:00) send-weekly-preview: not the right time (diff: 8160)',
            'INFO Executing daily (16:27:00) send-deadline-warning...',
            'INFO Executing daily (17:30:00) send-daily-summary...',
            'DEBUG Not executing daily (18:30:00) send-reminders: not the right time (diff: -3600)',
            'INFO Executing sync-strava (every 10 minutes)...',
            'DEBUG Stopping workers...',
            'DEBUG Consume messages...',
            'DEBUG Ran continuously.',
            "INFO Successfully ran command Olz\\Command\\OnContinuouslyCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Running command Olz\Command\OnContinuouslyCommand...
            Running continuously...
            Continuously processing email...
            Executing daily (16:27:00) send-deadline-warning...
            Executing daily (17:30:00) send-daily-summary...
            Executing sync-strava (every 10 minutes)...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 17:30:00'],
            ['send-deadline-warning', '2020-03-13 17:30:00'],
            ['send-daily-summary', '2020-03-13 17:30:00'],
            ['sync-strava', '2020-03-13 17:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:send-deadline-warning ',
            'olz:send-daily-summary ',
            'olz:sync-strava 2025',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandExecuteSome(): void {
        WithUtilsCache::get('dateUtils')->testOnlySetDate('2020-03-13 01:13:00');
        $throttling_repo = $this->setUpThrottling('2020-03-12 03:13:00');
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'INFO Executing daily (01:00:00) clean-temp-directory...',
            'INFO Executing daily (01:05:00) clean-temp-database...',
            'INFO Executing daily (01:10:00) clean-logs...',
            // The rest is not executed yet, because it's only 01:13
            'DEBUG Not executing daily (01:15:00) send-telegram-configuration: not the right time (diff: -120)',
            'DEBUG Not executing daily (01:20:00) sync-solv: not the right time (diff: -420)',
            'DEBUG Not executing daily (08:15:00) send-weekly-summary: not the right time (diff: -25320)',
            'DEBUG Not executing daily (14:30:00) send-monthly-preview: not the right time (diff: 38580)',
            'DEBUG Not executing daily (15:14:00) send-weekly-preview: not the right time (diff: 35940)',
            'DEBUG Not executing daily (16:27:00) send-deadline-warning: not the right time (diff: 31560)',
            'DEBUG Not executing daily (17:30:00) send-daily-summary: not the right time (diff: 27780)',
            'DEBUG Not executing daily (18:30:00) send-reminders: not the right time (diff: 24180)',
            'INFO Executing sync-strava (every 10 minutes)...',
            'DEBUG Stopping workers...',
            'DEBUG Consume messages...',
            'DEBUG Ran continuously.',
            "INFO Successfully ran command Olz\\Command\\OnContinuouslyCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Running command Olz\Command\OnContinuouslyCommand...
            Running continuously...
            Continuously processing email...
            Executing daily (01:00:00) clean-temp-directory...
            Executing daily (01:05:00) clean-temp-database...
            Executing daily (01:10:00) clean-logs...
            Executing sync-strava (every 10 minutes)...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 01:13:00'],
            ['clean-temp-directory', '2020-03-13 01:13:00'],
            ['clean-temp-database', '2020-03-13 01:13:00'],
            ['clean-logs', '2020-03-13 01:13:00'],
            ['sync-strava', '2020-03-13 01:13:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:clean-temp-directory ',
            'olz:clean-temp-database ',
            'olz:clean-logs ',
            'olz:sync-strava 2025',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testGetTimeOnlyDiffSeconds(): void {
        $command = new OnContinuouslyCommand();
        $this->assertSame(3600, $command->getTimeOnlyDiffSeconds('09:00:00', '08:00:00'));
        $this->assertSame(-32400, $command->getTimeOnlyDiffSeconds('2020-03-16 09:00:00', '2020-03-13 18:00:00'));
        $this->assertSame(32400, $command->getTimeOnlyDiffSeconds('2020-03-13 18:00:00', '2020-03-16 09:00:00'));
        $this->assertSame(32400, $command->getTimeOnlyDiffSeconds('18:00:00', '09:00:00'));
        $this->assertSame(-32400, $command->getTimeOnlyDiffSeconds('09:00:00', '18:00:00'));
        $this->assertSame(40271, $command->getTimeOnlyDiffSeconds('23:23:23', '12:12:12'));
        $this->assertSame(-40271, $command->getTimeOnlyDiffSeconds('12:12:12', '23:23:23'));

        // Daylight saving time boundary (ignores it!)
        $this->assertSame(3660, $command->getTimeOnlyDiffSeconds('2020-03-29 03:00:30', '2020-03-29 01:59:30'));
        $this->assertSame(3660, $command->getTimeOnlyDiffSeconds('2020-10-25 03:00:30', '2020-10-25 01:59:30'));
    }

    // ---

    protected function setUpThrottling(false|string $date): FakeThrottlingRepository {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->last_occurrences = [
            'clean-temp-directory' => $date,
            'clean-temp-database' => $date,
            'clean-logs' => $date,
            'send-telegram-configuration' => $date,
            'sync-solv' => $date,
            'sync-strava' => $date,
            'send-daily-summary' => $date,
            'send-deadline-warning' => $date,
            'send-monthly-preview' => $date,
            'send-reminders' => $date,
            'send-weekly-preview' => $date,
            'send-weekly-summary' => $date,
        ];
        return $throttling_repo;
    }
}
