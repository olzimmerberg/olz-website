<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnContinuouslyCommand;
use Olz\Entity\Throttling;
use Olz\Tests\Fake\Entity\FakeThrottlingRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
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
        $throttling_repo = $this->setUpThrottling('2020-03-13 18:30:00'); // just an hour ago
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
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
            ['on_continuously', '2020-03-13 19:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandFirstOccurrence(): void {
        $throttling_repo = $this->setUpThrottling(false); // never occurred before
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'DEBUG Executing daily (01:00:00) clean-temp-directory...',
            'DEBUG Executing daily (01:05:00) clean-temp-database...',
            'DEBUG Executing daily (01:10:00) clean-logs...',
            'DEBUG Executing daily (01:15:00) send-telegram-configuration...',
            'DEBUG Executing daily (01:20:00) sync-solv...',
            'DEBUG Executing daily (01:25:00) send-test-email...',
            'DEBUG Executing daily (16:27:00) send-daily-notifications...',
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
            Executing daily (01:25:00) send-test-email...
            Executing daily (16:27:00) send-daily-notifications...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 19:30:00'],
            ['clean-temp-directory', '2020-03-13 19:30:00'],
            ['clean-temp-database', '2020-03-13 19:30:00'],
            ['clean-logs', '2020-03-13 19:30:00'],
            ['send-telegram-configuration', '2020-03-13 19:30:00'],
            ['sync-solv', '2020-03-13 19:30:00'],
            ['send-test-email', '2020-03-13 19:30:00'],
            ['send-daily-notifications', '2020-03-13 19:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:clean-temp-directory ',
            'olz:clean-temp-database ',
            'olz:clean-logs ',
            'olz:send-telegram-configuration ',
            'olz:sync-solv ',
            'olz:send-test-email ',
            'olz:send-daily-notifications ',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandExecuteAll(): void {
        $throttling_repo = $this->setUpThrottling('2020-03-12 19:30:00'); // a day ago
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'DEBUG Executing daily (01:00:00) clean-temp-directory...',
            'DEBUG Executing daily (01:05:00) clean-temp-database...',
            'DEBUG Executing daily (01:10:00) clean-logs...',
            'DEBUG Executing daily (01:15:00) send-telegram-configuration...',
            'DEBUG Executing daily (01:20:00) sync-solv...',
            'DEBUG Executing daily (01:25:00) send-test-email...',
            'DEBUG Executing daily (16:27:00) send-daily-notifications...',
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
            Executing daily (01:25:00) send-test-email...
            Executing daily (16:27:00) send-daily-notifications...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 19:30:00'],
            ['clean-temp-directory', '2020-03-13 19:30:00'],
            ['clean-temp-database', '2020-03-13 19:30:00'],
            ['clean-logs', '2020-03-13 19:30:00'],
            ['send-telegram-configuration', '2020-03-13 19:30:00'],
            ['sync-solv', '2020-03-13 19:30:00'],
            ['send-test-email', '2020-03-13 19:30:00'],
            ['send-daily-notifications', '2020-03-13 19:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:clean-temp-directory ',
            'olz:clean-temp-database ',
            'olz:clean-logs ',
            'olz:send-telegram-configuration ',
            'olz:sync-solv ',
            'olz:send-test-email ',
            'olz:send-daily-notifications ',
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
            'DEBUG Executing daily (01:00:00) clean-temp-directory...',
            'DEBUG Executing daily (01:05:00) clean-temp-database...',
            'DEBUG Executing daily (01:10:00) clean-logs...',
            // The rest is not executed yet, because it's only 01:13
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
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:clean-temp-directory ',
            'olz:clean-temp-database ',
            'olz:clean-logs ',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
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
            'send-test-email' => $date,
            'send-daily-notifications' => $date,
        ];
        return $throttling_repo;
    }
}
