<?php

declare(strict_types=1);

use Olz\Apps\Logs\Endpoints\GetLogsEndpoint;

require_once __DIR__.'/../../../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
class GetLogsEndpointForIntegrationTest extends GetLogsEndpoint {
    public function testOnlyScandir($path, $sorting) {
        return $this->scandir($path, $sorting);
    }

    public function testOnlyReadFile($path) {
        return $this->readFile($path);
    }
}

/**
 * @internal
 * @covers \Olz\Apps\Logs\Endpoints\GetLogsEndpoint
 */
final class GetLogsEndpointIntegrationTest extends IntegrationTestCase {
    public function testScandir(): void {
        $endpoint = new GetLogsEndpointForIntegrationTest();
        $result = $endpoint->testOnlyScandir(__DIR__, SCANDIR_SORT_DESCENDING);
        $count = count($result);
        $this->assertSame('.', $result[$count - 1]);
        $this->assertSame('..', $result[$count - 2]);
        $this->assertSame(true, array_search(basename(__FILE__), $result) !== false);
    }

    public function testReadFile(): void {
        $endpoint = new GetLogsEndpointForIntegrationTest();
        $path = __DIR__.'/../../../document-root/temp/get_logs_endpoint.txt';
        file_put_contents($path, 'some content');
        $this->assertSame('some content', $endpoint->testOnlyReadFile($path));
        unlink($path);
    }
}
