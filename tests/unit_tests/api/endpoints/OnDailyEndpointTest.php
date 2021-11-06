<?php

declare(strict_types=1);

use Monolog\Logger;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../src/api/endpoints/OnDailyEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../../fake/FakeTask.php';
require_once __DIR__.'/../../../fake/FakeTelegramUtils.php';
require_once __DIR__.'/../../../fake/FakeThrottlingRepository.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

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
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $throttling_repo->last_daily_notifications = '2020-03-13 19:30:00';
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(429, $err->getCode());
        }
    }

    public function testOnDailyEndpointNoThrottlingRecord(): void {
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $throttling_repo->last_daily_notifications = null;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
        }
    }

    public function testOnDailyEndpointUnlimitedCron(): void {
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $throttling_repo->last_daily_notifications = '2020-03-13 19:30:00';
        $server_config->has_unlimited_cron = true;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
        }
    }

    public function testOnDailyEndpointWrongToken(): void {
        $sync_solv_task = new FakeTask();
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
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
        $clean_temp_directory_task = new FakeTask();
        $sync_solv_task = new FakeTask();
        $entity_manager = new FakeEntityManager();
        $throttling_repo = new FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories['Throttling'] = $throttling_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeEnvUtils();
        $telegram_utils = new FakeTelegramUtils();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setCleanTempDirectoryTask($clean_temp_directory_task);
        $endpoint->setSyncSolvTask($sync_solv_task);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([['on_daily', '2020-03-13 19:30:00']], $throttling_repo->recorded_occurrences);
        $this->assertSame([], $result);
        $this->assertSame(true, $clean_temp_directory_task->hasBeenRun);
        $this->assertSame(true, $sync_solv_task->hasBeenRun);
        $this->assertSame(true, $telegram_utils->configurationSent);
    }
}
