<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\OnContinuouslyEndpoint;
use Olz\Entity\Throttling;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\OnContinuouslyEndpoint
 */
final class OnContinuouslyEndpointTest extends UnitTestCase {
    public function testOnContinuouslyEndpointIdent(): void {
        $endpoint = new OnContinuouslyEndpoint();
        $this->assertSame('OnContinuouslyEndpoint', $endpoint->getIdent());
    }

    public function testOnContinuouslyEndpointParseInput(): void {
        global $_GET;
        $_GET = ['authenticityCode' => 'some-token'];
        $endpoint = new OnContinuouslyEndpoint();
        $parsed_input = $endpoint->parseInput();
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnContinuouslyEndpointWrongToken(): void {
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new Fake\FakeEnvUtils());
        $endpoint->setSymfonyUtils($symfony_utils);

        try {
            $result = $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
            $this->assertSame([], $symfony_utils->commandsCalled);
        }
    }

    public function testOnContinuouslyEndpointTooSoonToSendDailyEmails(): void {
        $process_email_task = new Fake\FakeTask();
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new Fake\FakeEnvUtils());
        $endpoint->setSymfonyUtils($symfony_utils);
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_daily_notifications = '2020-03-13 18:30:00'; // just an hour ago
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $result);
        $this->assertSame([], $throttling_repo->recorded_occurrences);
        $this->assertSame(true, $process_email_task->hasBeenRun);
        $this->assertSame(['olz:onContinuously'], $symfony_utils->commandsCalled);
    }

    public function testOnContinuouslyEndpointFirstDailyNotifications(): void {
        $send_daily_notifications_task = new Fake\FakeTask();
        $process_email_task = new Fake\FakeTask();
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new Fake\FakeEnvUtils());
        $endpoint->setSymfonyUtils($symfony_utils);
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_daily_notifications = null;
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $result);
        $this->assertSame(
            [['daily_notifications', '2020-03-13 19:30:00']],
            $throttling_repo->recorded_occurrences
        );
        $this->assertSame(true, $send_daily_notifications_task->hasBeenRun);
        $this->assertSame(true, $process_email_task->hasBeenRun);
        $this->assertSame(['olz:onContinuously'], $symfony_utils->commandsCalled);
    }

    public function testOnContinuouslyEndpoint(): void {
        $send_daily_notifications_task = new Fake\FakeTask();
        $process_email_task = new Fake\FakeTask();
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new Fake\FakeEnvUtils());
        $endpoint->setSymfonyUtils($symfony_utils);
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $result);
        $this->assertSame(
            [['daily_notifications', '2020-03-13 19:30:00']],
            $throttling_repo->recorded_occurrences
        );
        $this->assertSame(true, $send_daily_notifications_task->hasBeenRun);
        $this->assertSame(true, $process_email_task->hasBeenRun);
        $this->assertSame(['olz:onContinuously'], $symfony_utils->commandsCalled);
    }
}
