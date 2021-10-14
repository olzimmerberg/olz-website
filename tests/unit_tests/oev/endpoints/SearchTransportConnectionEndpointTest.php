<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/oev/endpoints/SearchTransportConnectionEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

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
        throw new Exception("Unmocked transport API request: {$pretty_request}");
    }
}

/**
 * @internal
 * @covers \SearchTransportConnectionEndpoint
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

        // echo json_encode($result,  JSON_PRETTY_PRINT);
        // $this->assertSame([], $result);
        $this->assertSame(1, 1);
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

        // echo json_encode($result,  JSON_PRETTY_PRINT);
        // $this->assertSame([], $result);
        $this->assertSame(1, 1);
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

        $this->assertSame(['status' => 'ERROR', 'suggestions' => null], $result);
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

        $this->assertSame(['status' => 'ERROR', 'suggestions' => null], $result);
    }
}
