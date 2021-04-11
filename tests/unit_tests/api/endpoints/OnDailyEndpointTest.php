<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/OnDailyEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeOnDailyEndpointSyncSolvTask {
    public $hasBeenRun = false;

    public function run() {
        $this->hasBeenRun = true;
    }
}

class FakeOnDailyEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }
}

class FakeOnDailyEndpointThrottlingRepository {
    public $throttled = false;
    public $recorded_occurrences = [];

    public function getLastOccurrenceOf($event_name) {
        if ($event_name == 'on_daily') {
            if ($this->throttled === true) {
                return new DateTime('2020-03-13 19:30:00');
            }
            if ($this->throttled === null) {
                return null;
            }
            return new DateTime('2020-03-12 20:30:00');
        }
        throw new Exception("Unexpected event name");
    }

    public function recordOccurrenceOf($event_name, $datetime) {
        $this->recorded_occurrences[] = [$event_name, $datetime];
    }
}

class FakeOnDailyEndpointEnvUtils {
    public $has_unlimited_cron = false;

    public function hasUnlimitedCron() {
        return $this->has_unlimited_cron;
    }

    public function getCronAuthenticityCode() {
        return 'some-token';
    }
}

/**
 * @internal
 * @covers \OnDailyEndpoint
 */
final class OnDailyEndpointTest extends UnitTestCase {
    public function testOnDailyEndpointIdent(): void {
        $endpoint = new OnDailyEndpoint();
        $this->assertSame('OnDailyEndpoint', $endpoint->getIdent());
    }

    public function testOnDailyEndpointParseInput(): void {
        global $_GET;
        $_GET = ['authenticityCode' => 'some-token'];
        $endpoint = new OnDailyEndpoint();
        $parsed_input = $endpoint->parseInput();
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnDailyEndpointThrottled(): void {
        $entity_manager = new FakeOnDailyEndpointEntityManager();
        $throttling_repo = new FakeOnDailyEndpointThrottlingRepository();
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $throttling_repo->throttled = true;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(429, $err->getCode());
        }
    }

    public function testOnDailyEndpointNoThrottlingRecord(): void {
        $entity_manager = new FakeOnDailyEndpointEntityManager();
        $throttling_repo = new FakeOnDailyEndpointThrottlingRepository();
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $throttling_repo->throttled = null;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
        }
    }

    public function testOnDailyEndpointUnlimitedCron(): void {
        $entity_manager = new FakeOnDailyEndpointEntityManager();
        $throttling_repo = new FakeOnDailyEndpointThrottlingRepository();
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $throttling_repo->throttled = true;
        $server_config->has_unlimited_cron = true;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
        }
    }

    public function testOnDailyEndpointWrongToken(): void {
        $sync_solv_task = new FakeOnDailyEndpointSyncSolvTask();
        $entity_manager = new FakeOnDailyEndpointEntityManager();
        $throttling_repo = new FakeOnDailyEndpointThrottlingRepository();
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setSyncSolvTask($sync_solv_task);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testOnDailyEndpoint(): void {
        $sync_solv_task = new FakeOnDailyEndpointSyncSolvTask();
        $entity_manager = new FakeOnDailyEndpointEntityManager();
        $throttling_repo = new FakeOnDailyEndpointThrottlingRepository();
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setSyncSolvTask($sync_solv_task);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([['on_daily', '2020-03-13 19:30:00']], $throttling_repo->recorded_occurrences);
        $this->assertSame([], $result);
        $this->assertSame(true, $sync_solv_task->hasBeenRun);
    }
}
