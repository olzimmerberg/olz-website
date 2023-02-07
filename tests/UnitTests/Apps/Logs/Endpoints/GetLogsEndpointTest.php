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
        $fake_content = [];
        for ($i = 0; $i < 144; $i++) {
            $iso_date = date('Y-m-d H:i:s', strtotime('2020-03-12') + $i * 600);
            $fake_content[] = "[{$iso_date}] tick 2020-03-12\n";
        }
        file_put_contents(
            __DIR__.'/../../../tmp/logs/merged-2020-03-12.log',
            implode('', $fake_content),
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
                'channel' => 'olz-logs',
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
            'INFO log_file_before data-path/logs/merged-2020-03-12.log',
            'INFO log_file_after data-path/logs/merged-2020-03-14.log',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            ...array_slice($fake_content, 144 - 97, 97),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
            "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
            "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
            '---',
            "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        ], $result['content']);
        $previous = json_decode($result['pagination']['previous'], true);
        $this->assertMatchesRegularExpression(
            '/\/tmp\/logs\/merged-2020-03-12.log$/',
            $previous['logFile'],
        );
        $this->assertSame(144 - 97 - 1, $previous['lineNumber']);
        $this->assertSame(null, $result['pagination']['next']);
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
                    'channel' => 'olz-logs',
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
                    'channel' => 'olz-logs',
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
