<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Monolog\Logger;
use Olz\Api\Endpoints\OnContinuouslyEndpoint;
use Olz\Entity\Throttling;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeTask;
use Olz\Tests\Fake\FakeThrottlingRepository;
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
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeEnvUtils());

        try {
            $result = $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testOnContinuouslyEndpointTooSoonToSendDailyEmails(): void {
        $process_email_task = new FakeTask();
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeEnvUtils());
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_daily_notifications = '2020-03-13 18:30:00'; // just an hour ago
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame([], $throttling_repo->recorded_occurrences);
        $this->assertSame(true, $process_email_task->hasBeenRun);
    }

    public function testOnContinuouslyEndpointFirstDailyNotifications(): void {
        $send_daily_notifications_task = new FakeTask();
        $process_email_task = new FakeTask();
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeEnvUtils());
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $throttling_repo->last_daily_notifications = null;
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame(
            [['daily_notifications', '2020-03-13 19:30:00']],
            $throttling_repo->recorded_occurrences
        );
        $this->assertSame(true, $send_daily_notifications_task->hasBeenRun);
        $this->assertSame(true, $process_email_task->hasBeenRun);
    }

    public function testOnContinuouslyEndpoint(): void {
        $send_daily_notifications_task = new FakeTask();
        $process_email_task = new FakeTask();
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeEnvUtils());
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'daily_notifications';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame(
            [['daily_notifications', '2020-03-13 19:30:00']],
            $throttling_repo->recorded_occurrences
        );
        $this->assertSame(true, $send_daily_notifications_task->hasBeenRun);
        $this->assertSame(true, $process_email_task->hasBeenRun);
    }
}
