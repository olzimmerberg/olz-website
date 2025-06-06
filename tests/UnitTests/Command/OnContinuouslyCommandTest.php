<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnContinuouslyCommand;
use Olz\Entity\Throttling;
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
    public function testOnContinuouslyCommandTooSoonToSendDailyEmails(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_occurrence = '2020-03-13 18:30:00'; // just an hour ago
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

    public function testOnContinuouslyCommandFirstDailyNotifications(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_occurrence = null;
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'DEBUG Sending daily mail at 2020-03-13 19:30:00...',
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
            Sending daily mail at 2020-03-13 19:30:00...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 19:30:00'],
            ['daily_notifications', '2020-03-13 19:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:send-daily-notifications ',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testOnContinuouslyCommandSendDailyNotifications(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'daily_notifications';
        $command = new OnContinuouslyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnContinuouslyCommand...",
            'DEBUG Running continuously...',
            'DEBUG Continuously processing email...',
            'DEBUG Sending daily mail at 2020-03-13 19:30:00...',
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
            Sending daily mail at 2020-03-13 19:30:00...
            Stopping workers...
            Consume messages...
            Ran continuously.
            Successfully ran command Olz\Command\OnContinuouslyCommand.

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            ['on_continuously', '2020-03-13 19:30:00'],
            ['daily_notifications', '2020-03-13 19:30:00'],
        ], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:process-email ',
            'olz:send-daily-notifications ',
            'messenger:stop-workers ',
            'messenger:consume async --no-reset=--no-reset',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
