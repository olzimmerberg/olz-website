<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\OnDailyEndpoint;
use Olz\Entity\Throttling;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\OnDailyEndpoint
 */
final class OnDailyEndpointTest extends UnitTestCase {
    public function testOnDailyEndpointIdent(): void {
        $endpoint = new OnDailyEndpoint();
        $this->assertSame('OnDailyEndpoint', $endpoint->getIdent());
    }

    public function testOnDailyEndpointParseInput(): void {
        $get_params = ['authenticityCode' => 'some-token'];
        $request = new Request($get_params);
        $endpoint = new OnDailyEndpoint();
        $parsed_input = $endpoint->parseInput($request);
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnDailyEndpointThrottled(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $server_config = new Fake\FakeEnvUtils();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setLog($logger);

        $throttling_repo->last_daily_notifications = '2020-03-13 19:30:00';
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'ERROR Throttled user request',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(429, $err->getCode());
            $this->assertSame([], $symfony_utils->commandsCalled);
        }
    }

    public function testOnDailyEndpointNoThrottlingRecord(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $server_config = new Fake\FakeEnvUtils();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setLog($logger);

        $throttling_repo->last_daily_notifications = null;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'WARNING Bad user request',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
            $this->assertSame([], $symfony_utils->commandsCalled);
        }
    }

    public function testOnDailyEndpointUnlimitedCron(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $server_config = new Fake\FakeEnvUtils();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setLog($logger);

        $throttling_repo->last_daily_notifications = '2020-03-13 19:30:00';
        $server_config->has_unlimited_cron = true;
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'WARNING Bad user request',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
            $this->assertSame([], $symfony_utils->commandsCalled);
        }
    }

    public function testOnDailyEndpointWrongToken(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $server_config = new Fake\FakeEnvUtils();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setLog($logger);

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

    public function testOnDailyEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $throttling_repo = new Fake\FakeThrottlingRepository();
        $throttling_repo->expected_event_name = 'on_daily';
        $entity_manager->repositories[Throttling::class] = $throttling_repo;
        $server_config = new Fake\FakeEnvUtils();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $telegram_utils = new Fake\FakeTelegramUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new OnDailyEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([['on_daily', '2020-03-13 19:30:00']], $throttling_repo->recorded_occurrences);
        $this->assertSame([], $result);
        $this->assertSame([
            ['olz:on-daily', ''],
        ], $symfony_utils->commandsCalled);
        $this->assertSame(true, $telegram_utils->configurationSent);
    }
}
