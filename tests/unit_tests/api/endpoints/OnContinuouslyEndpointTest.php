<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/OnContinuouslyEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeOnContinuouslyEndpointSendDailyNotificationsTask {
    public $hasBeenRun = false;

    public function run() {
        $this->hasBeenRun = true;
    }
}

class FakeOnContinuouslyEndpointProcessEmailTask {
    public $hasBeenRun = false;

    public function run() {
        $this->hasBeenRun = true;
    }
}

class FakeOnContinuouslyEndpointEnvUtils {
    public function getCronAuthenticityCode() {
        return 'some-token';
    }
}

class FakeOnContinuouslyEndpointThrottlingRepository {
    public $last_daily_notifications = '2020-03-12 19:30:00';
    public $num_occurrences_recorded = 0;

    public function getLastOccurrenceOf($event_name) {
        if ($event_name == 'daily_notifications') {
            if (!$this->last_daily_notifications) {
                return null;
            }
            return new DateTime($this->last_daily_notifications);
        }
        throw new Exception("this should never happen");
    }

    public function recordOccurrenceOf($event_name, $datetime) {
        if ($event_name == 'daily_notifications') {
            $this->num_occurrences_recorded++;
            return;
        }
        throw new Exception("this should never happen");
    }
}

/**
 * @internal
 * @covers \OnContinuouslyEndpoint
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
        $endpoint->setLogger($logger);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeOnContinuouslyEndpointEnvUtils());

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
        $process_email_task = new FakeOnContinuouslyEndpointProcessEmailTask();
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLogger($logger);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeOnContinuouslyEndpointEnvUtils());
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeOnContinuouslyEndpointThrottlingRepository();
        $throttling_repo->last_daily_notifications = '2020-03-13 18:30:00'; // just an hour ago
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame(0, $throttling_repo->num_occurrences_recorded);
        $this->assertSame(true, $process_email_task->hasBeenRun);
    }

    public function testOnContinuouslyEndpointFirstDailyNotifications(): void {
        $send_daily_notifications_task = new FakeOnContinuouslyEndpointSendDailyNotificationsTask();
        $process_email_task = new FakeOnContinuouslyEndpointProcessEmailTask();
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLogger($logger);
        $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeOnContinuouslyEndpointEnvUtils());
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeOnContinuouslyEndpointThrottlingRepository();
        $throttling_repo->last_daily_notifications = null;
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame(1, $throttling_repo->num_occurrences_recorded);
        $this->assertSame(true, $send_daily_notifications_task->hasBeenRun);
        $this->assertSame(true, $process_email_task->hasBeenRun);
    }

    public function testOnContinuouslyEndpoint(): void {
        $send_daily_notifications_task = new FakeOnContinuouslyEndpointSendDailyNotificationsTask();
        $process_email_task = new FakeOnContinuouslyEndpointProcessEmailTask();
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLogger($logger);
        $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
        $endpoint->setProcessEmailTask($process_email_task);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new FakeOnContinuouslyEndpointEnvUtils());
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeOnContinuouslyEndpointThrottlingRepository();
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame(1, $throttling_repo->num_occurrences_recorded);
        $this->assertSame(true, $send_daily_notifications_task->hasBeenRun);
        $this->assertSame(true, $process_email_task->hasBeenRun);
    }
}
