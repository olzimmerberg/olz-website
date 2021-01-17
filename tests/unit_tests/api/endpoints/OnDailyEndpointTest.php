<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/OnDailyEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeOnDailyEndpointSyncSolvTask {
    public $hasBeenRun = false;

    public function run() {
        $this->hasBeenRun = true;
    }
}

class FakeOnDailyEndpointServerConfig {
    public function getCronAuthenticityCode() {
        return 'some-token';
    }
}

/**
 * @internal
 * @covers \OnDailyEndpoint
 */
final class OnDailyEndpointTest extends TestCase {
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

    public function testOnDailyEndpointWrongToken(): void {
        $sync_solv_task = new FakeOnDailyEndpointSyncSolvTask();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointServerConfig();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setSyncSolvTask($sync_solv_task);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setServerConfig($server_config);
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
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $server_config = new FakeOnDailyEndpointServerConfig();
        $logger = new Logger('OnDailyEndpointTest');
        $endpoint = new OnDailyEndpoint();
        $endpoint->setSyncSolvTask($sync_solv_task);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setServerConfig($server_config);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
        $this->assertSame(true, $sync_solv_task->hasBeenRun);
    }
}
