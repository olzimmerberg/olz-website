<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Endpoints;

use Olz\Apps\Oev\Endpoints\SearchTransportConnectionEndpoint;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;

require_once __DIR__.'/../../../../Fake/fake_role.php';

class FakeSearchTransportConnectionEndpointTransportApiFetcher {
    public function fetchConnection($request_data) {
        $from = str_replace(' ', '_', $request_data['from']);
        $to = str_replace(' ', '_', $request_data['to']);
        $date = $request_data['date'];
        $time = str_replace(':', '-', $request_data['time']);
        $file_path = __DIR__."/from_{$from}_to_{$to}_at_{$date}T{$time}.json";
        if (is_file($file_path)) {
            return json_decode(file_get_contents($file_path), true);
        }
        $pretty_request = json_encode($request_data);
        throw new \Exception("Unmocked transport API request: {$pretty_request}");
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Endpoints\SearchTransportConnectionEndpoint
 */
final class SearchTransportConnectionEndpointTest extends UnitTestCase {
    public function testSearchTransportConnectionEndpointIdent(): void {
        $endpoint = new SearchTransportConnectionEndpoint();
        $this->assertSame('SearchTransportConnectionEndpoint', $endpoint->getIdent());
    }

    public function testSearchTransportConnectionEndpointExample1(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $logger = FakeLogger::create();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'destination' => 'Winterthur',
            'arrival' => '2021-09-22 09:00:00',
        ]);

        $this->assertSame('OK', $result['status']);
        $this->assertSame(8, count($result['suggestions']));
        $suggestion0 = $result['suggestions'][0];
        $this->assertSame(1, count($suggestion0['mainConnection']['sections']));
        $this->assertSame([2, 3], array_map(function ($side_connection) {
            return count($side_connection['connection']['sections']);
        }, $suggestion0['sideConnections']));
        $this->assertSame(['8503202', '8503000'], array_map(function ($side_connection) {
            return $side_connection['joiningStationId'];
        }, $suggestion0['sideConnections']));
        $this->assertSame([], $suggestion0['originInfo']);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
    }

    public function testSearchTransportConnectionEndpointExample2(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $logger = FakeLogger::create();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'destination' => 'Flumserberg Tannenheim',
            'arrival' => '2021-10-01 13:15:00',
        ]);

        $this->assertSame('OK', $result['status']);
        $this->assertSame(0, count($result['suggestions']));
        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
    }

    public function testSearchTransportConnectionEndpointFailingRequest(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $logger = FakeLogger::create();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'destination' => 'inexistent',
            'arrival' => '2021-10-01 13:15:00',
        ]);

        $this->assertSame([
            'status' => 'ERROR',
            'suggestions' => null,
        ], $result);
        $this->assertSame([
            'INFO Valid user request',
            'ERROR Exception: Unmocked transport API request: {"from":"8503207","to":"inexistent","date":"2021-10-01","time":"13:15","isArr',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords(function ($record, $level_name, $message) {
            $cropped_message = substr($message, 0, 120);
            return "{$level_name} {$cropped_message}";
        }));
    }

    public function testSearchTransportConnectionEndpointNoAccess(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = FakeLogger::create();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'destination' => 'Flumserberg Tannenheim',
            'arrival' => '2021-10-01 13:15:00',
        ]);

        $this->assertSame([
            'status' => 'ERROR',
            'suggestions' => null,
        ], $result);
        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
    }
}
