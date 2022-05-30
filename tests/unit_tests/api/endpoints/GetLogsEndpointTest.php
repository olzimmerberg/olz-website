<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/api/endpoints/GetLogsEndpoint.php';
require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../_/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

use Monolog\Logger;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 * @coversNothing
 */
class GetLogsEndpointForTest extends GetLogsEndpoint {
    public $scandir_calls = [];
    public $read_file_calls = [];

    protected function scandir($path, $sorting) {
        $this->scandir_calls[] = [$path, $sorting];
        return [
            'merged-2022-03-12.log',
            'merged-2022-03-13.log',
        ];
    }

    protected function readFile($path) {
        $this->read_file_calls[] = [$path];
        $basename = basename($path);
        return "test log entry in {$basename}";
    }
}

/**
 * @internal
 * @covers \GetLogsEndpoint
 */
final class GetLogsEndpointTest extends UnitTestCase {
    public function testGetLogsEndpointIdent(): void {
        $endpoint = new GetLogsEndpoint();
        $this->assertSame('GetLogsEndpoint', $endpoint->getIdent());
    }

    public function testGetLogsEndpoint(): void {
        $logger = new Logger('GetLogsEndpointTest');
        $endpoint = new GetLogsEndpointForTest();
        $env_utils = new FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['index' => 1]);

        $this->assertSame([
            'content' => 'test log entry in merged-2022-03-13.log',
        ], $result);
    }

    public function testGetLogsEndpointNotAuthorized(): void {
        $logger = new Logger('GetLogsEndpointTest');
        $endpoint = new GetLogsEndpointForTest();
        $env_utils = new FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(['index' => 0]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }

    public function testGetLogsEndpointNotAuthenticated(): void {
        $logger = new Logger('GetLogsEndpointTest');
        $endpoint = new GetLogsEndpointForTest();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(['index' => 0]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }
}
