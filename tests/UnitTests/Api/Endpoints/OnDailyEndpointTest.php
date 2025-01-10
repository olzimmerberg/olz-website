<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\OnDailyEndpoint;
use Olz\Entity\Throttling;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\OnDailyEndpoint
 */
final class OnDailyEndpointTest extends UnitTestCase {
    public function testOnDailyEndpointParseInput(): void {
        $get_params = ['authenticityCode' => 'some-token'];
        $request = new Request($get_params);
        $endpoint = new OnDailyEndpoint();
        $endpoint->runtimeSetup();
        $parsed_input = $endpoint->parseInput($request);
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnDailyEndpointThrottled(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'on_daily';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $endpoint = new OnDailyEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'ERROR Throttled user request',
            ], $this->getLogs());
            $this->assertSame(429, $err->getCode());
            $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
        }
    }

    public function testOnDailyEndpointNoThrottlingRecord(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'on_daily';
        $throttling_repo->last_occurrence = null;
        $endpoint = new OnDailyEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'WARNING Bad user request',
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
            $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
        }
    }

    public function testOnDailyEndpointUnlimitedCron(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'on_daily';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $endpoint = new OnDailyEndpoint();
        $endpoint->runtimeSetup();

        WithUtilsCache::get('envUtils')->has_unlimited_cron = true;
        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'WARNING Bad user request',
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode()); // in other words: it wasn't throttled
            $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
        }
    }

    public function testOnDailyEndpointWrongToken(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'on_daily';
        $endpoint = new OnDailyEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
            $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
        }
    }

    public function testOnDailyEndpoint(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'on_daily';
        $endpoint = new OnDailyEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([['on_daily', '2020-03-13 19:30:00']], $throttling_repo->recorded_occurrences);
        $this->assertSame([], $result);
        $this->assertSame([
            'olz:on-daily ',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
