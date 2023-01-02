<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Endpoints;

use Olz\Apps\Logs\Endpoints\GetLogsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

// /**
//  * @internal
//  *
//  * @coversNothing
//  */
// class GetLogsEndpointForTest extends GetLogsEndpoint {
//     public $scandir_calls = [];
//     public $read_file_calls = [];

//     protected function scandir($path, $sorting) {
//         $this->scandir_calls[] = [$path, $sorting];
//         return [
//             'merged-2022-03-12.log',
//             'merged-2022-03-13.log',
//         ];
//     }

//     protected function readFile($path) {
//         $this->read_file_calls[] = [$path];
//         $basename = basename($path);
//         return "test log entry in {$basename}";
//     }
// }

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Endpoints\GetLogsEndpoint
 */
final class GetLogsEndpointTest extends UnitTestCase {
    public function testGetLogsEndpointIdent(): void {
        $endpoint = new GetLogsEndpoint();
        $this->assertSame('GetLogsEndpoint', $endpoint->getIdent());
    }

    public function testGetLogsEndpointTargetDate(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetLogsEndpoint();
        $env_utils = new Fake\FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../../tmp/logs/');
        file_put_contents(
            __DIR__.'/../../../tmp/logs/merged-2020-03-12.log',
            "[2020-03-12 12:00:00] tick 2020-03-12\n",
        );
        file_put_contents(
            __DIR__.'/../../../tmp/logs/merged-2020-03-13.log',
            implode('', [
                "[2020-03-13 12:00:00] tick 2020-03-13\n",
                "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
                "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
                "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            ]),
        );
        file_put_contents(
            __DIR__.'/../../../tmp/logs/merged-2020-03-14.log',
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        );

        $result = $endpoint->call([
            'query' => [
                'targetDate' => '2020-03-13 18:30:00',
                'firstDate' => null,
                'lastDate' => null,
                'minLogLevel' => null,
                'textSearch' => null,
                'pageToken' => null,
            ],
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Logs access by admin.',
            'INFO BinarySearch: 1 38 2020-03-13 18:30:00 <=> 2020-03-13 14:00:00 -> 1',
            'INFO BinarySearch: 2 97 2020-03-13 18:30:00 <=> 2020-03-13 18:00:00 -> 1',
            'INFO file_path_before data-path/logs/merged-2020-03-12.log',
            'INFO file_path_after data-path/logs/merged-2020-03-14.log',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'content' => [
                "[2020-03-12 12:00:00] tick 2020-03-12\n",
                "[2020-03-13 12:00:00] tick 2020-03-13\n",
                "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
                "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
                '---',
                "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
                "[2020-03-14 12:00:00] tick 2020-03-14\n",
            ],
            'pagination' => ['previous' => null, 'next' => null],
        ], $result);
    }

    public function testGetLogsEndpointNotAuthorized(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetLogsEndpoint();
        $env_utils = new Fake\FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                'query' => [
                    'targetDate' => null,
                    'firstDate' => null,
                    'lastDate' => null,
                    'minLogLevel' => null,
                    'textSearch' => null,
                    'pageToken' => null,
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }

    public function testGetLogsEndpointNotAuthenticated(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetLogsEndpoint();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                'query' => [
                    'targetDate' => null,
                    'firstDate' => null,
                    'lastDate' => null,
                    'minLogLevel' => null,
                    'textSearch' => null,
                    'pageToken' => null,
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }
}
