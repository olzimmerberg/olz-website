<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnContinuouslyCommand;
use Olz\Entity\Throttling;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class OnContinuouslyCommandForTest extends OnContinuouslyCommand {
    public $commandsCalled = [];

    public function callCommand(
        string $command_name,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        $this->commandsCalled[] = $command_name;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\OnContinuouslyCommand
 */
final class OnContinuouslyCommandTest extends UnitTestCase {
    public function testOnContinuouslyCommandTooSoonToSendDailyEmails(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_daily_notifications = '2020-03-13 18:30:00'; // just an hour ago
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $logger = Fake\FakeLogger::create();
        $command = new OnContinuouslyCommandForTest();
        $command->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $command->setEntityManager($entity_manager);
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest...",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame([], $throttling_repo->recorded_occurrences);
        $this->assertSame([
            'olz:test',
            'olz:processEmail',
        ], $command->commandsCalled);
    }

    public function testOnContinuouslyCommandFirstDailyNotifications(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_daily_notifications = null;
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $logger = Fake\FakeLogger::create();
        $command = new OnContinuouslyCommandForTest();
        $command->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $command->setEntityManager($entity_manager);
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest...",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame(
            [['daily_notifications', '2020-03-13 19:30:00']],
            $throttling_repo->recorded_occurrences
        );
        $this->assertSame([
            'olz:test',
            'olz:processEmail',
            'olz:sendDailyNotifications',
        ], $command->commandsCalled);
    }

    public function testOnContinuouslyCommandSendDailyNotifications(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $logger = Fake\FakeLogger::create();
        $command = new OnContinuouslyCommandForTest();
        $command->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $command->setEntityManager($entity_manager);
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest...",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame(
            [['daily_notifications', '2020-03-13 19:30:00']],
            $throttling_repo->recorded_occurrences
        );
        $this->assertSame([
            'olz:test',
            'olz:processEmail',
            'olz:sendDailyNotifications',
        ], $command->commandsCalled);
    }
}
