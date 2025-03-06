<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Endpoints;

use Olz\Apps\Logs\Endpoints\GetLogsEndpoint;
use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\LineLocation;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class TestOnlyGetLogsEndpoint extends GetLogsEndpoint {
    public function testOnlySerializePageToken(
        ?LineLocation $line_location,
        ?string $mode,
    ): ?string {
        return $this->serializePageToken($line_location, $mode);
    }

    /** @return array{lineLocation: LineLocation, mode: ?string} */
    public function testOnlyDeserializePageToken(
        string $serialized,
    ): array {
        return $this->deserializePageToken($serialized);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Endpoints\GetLogsEndpoint
 */
final class GetLogsEndpointTest extends UnitTestCase {
    public function testGetLogsEndpointTargetDate(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new GetLogsEndpoint();
        $endpoint->setup();

        $num_fake = intval(BaseLogsChannel::$pageSize);
        mkdir(__DIR__.'/../../../tmp/private/logs/', 0o777, true);
        $fake_content = [];
        for ($i = 0; $i < $num_fake; $i++) {
            $iso_date = date('Y-m-d H:i:s', strtotime('2020-03-12') + $i * 600);
            $fake_content[] = "[{$iso_date}] tick 2020-03-12\n";
        }
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/merged-2020-03-12.log',
            implode('', $fake_content),
        );
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/merged-2020-03-13.log',
            implode('', [
                "[2020-03-13 12:00:00] tick 2020-03-13\n",
                "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
                "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
                "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            ]),
        );
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/merged-2020-03-14.log',
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        );

        // Target is between two log entries
        $result = $endpoint->call([
            'query' => [
                'channel' => 'olz-logs',
                'targetDate' => '2020-03-13 19:29:00',
                'firstDate' => null,
                'lastDate' => null,
                'minLogLevel' => null,
                'textSearch' => null,
                'pageToken' => null,
            ],
        ]);
        $num_fake_on_page = intval(BaseLogsChannel::$pageSize / 2 - 3);
        $this->assertSame([
            ...array_slice($fake_content, $num_fake - $num_fake_on_page, $num_fake_on_page),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
            "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
            "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
            '---',
            "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        ], $result['content']);
        $previous = json_decode($result['pagination']['previous'], true);
        $this->assertMatchesRegularExpression(
            '/\\\\\/tmp\\\\\/private\\\\\/logs\\\\\/merged-2020-03-12\.log/',
            $previous['logFile'],
        );
        $this->assertSame($num_fake - $num_fake_on_page - 1, $previous['lineNumber']);
        $this->assertSame(-1, $previous['comparison']);
        $this->assertNull($result['pagination']['next']);

        // Target is exact date of one log entry
        $result = $endpoint->call([
            'query' => [
                'channel' => 'olz-logs',
                'targetDate' => '2020-03-13 18:00:00',
                'firstDate' => null,
                'lastDate' => null,
                'minLogLevel' => null,
                'textSearch' => null,
                'pageToken' => null,
            ],
        ]);
        $num_fake_on_page = intval(BaseLogsChannel::$pageSize / 2 - 2);
        $this->assertSame([
            ...array_slice($fake_content, $num_fake - $num_fake_on_page, $num_fake_on_page),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
            "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
            '---',
            "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
            "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        ], $result['content']);
        $previous = json_decode($result['pagination']['previous'], true);
        $this->assertMatchesRegularExpression(
            '/\\\\\/tmp\\\\\/private\\\\\/logs\\\\\/merged-2020-03-12\.log/',
            $previous['logFile'],
        );
        $this->assertSame($num_fake - $num_fake_on_page - 1, $previous['lineNumber']);
        $this->assertSame(-1, $previous['comparison']);
        $this->assertNull($result['pagination']['next']);

        // Target is early
        $result = $endpoint->call([
            'query' => [
                'channel' => 'olz-logs',
                'targetDate' => '2020-03-12 00:00:01',
                'firstDate' => null,
                'lastDate' => null,
                'minLogLevel' => null,
                'textSearch' => null,
                'pageToken' => null,
            ],
        ]);
        $this->assertSame([
            $fake_content[0],
            '---',
            ...array_slice($fake_content, 1, $num_fake - 1),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
        ], $result['content']);
        $this->assertNull($result['pagination']['previous']);
        $next = json_decode($result['pagination']['next'], true);
        $this->assertMatchesRegularExpression(
            '/\\\\\/tmp\\\\\/private\\\\\/logs\\\\\/merged-2020-03-13\.log/',
            $next['logFile'],
        );
        $this->assertSame(1, $next['lineNumber']);
        $this->assertSame(1, $next['comparison']);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Logs access by admin.',
            'DEBUG log_file_before private-path/logs/merged-2020-03-12.log',
            'DEBUG log_file_after private-path/logs/merged-2020-03-14.log',
            'ERROR HybridLogFile.php:*** Inexistent hybrid log file HybridLogFile(private-path/logs/merged-2020-03-15.log, private-path/logs/merged-2020-03-15.log.gz, private-path/logs/merged-2020-03-15.log, plain)',
            'INFO Valid user response',
            'INFO Valid user request',
            'INFO Logs access by admin.',
            'DEBUG log_file_before private-path/logs/merged-2020-03-12.log',
            'DEBUG log_file_after private-path/logs/merged-2020-03-14.log',
            'ERROR HybridLogFile.php:*** Inexistent hybrid log file HybridLogFile(private-path/logs/merged-2020-03-15.log, private-path/logs/merged-2020-03-15.log.gz, private-path/logs/merged-2020-03-15.log, plain)',
            'INFO Valid user response',
            'INFO Valid user request',
            'INFO Logs access by admin.',
            'ERROR HybridLogFile.php:*** Inexistent hybrid log file HybridLogFile(private-path/logs/merged-2020-03-11.log, private-path/logs/merged-2020-03-11.log.gz, private-path/logs/merged-2020-03-11.log, gz)',
            'DEBUG log_file_after private-path/logs/merged-2020-03-13.log',
            'INFO Valid user response',
        ], $this->getLogs());
    }

    public function testGetLogsEndpointNotAuthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $endpoint = new GetLogsEndpoint();
        $endpoint->setup();

        try {
            $endpoint->call([
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
            ], $this->getLogs());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }

    public function testGetLogsEndpointSerializePageToken(): void {
        $endpoint = new TestOnlyGetLogsEndpoint();
        $log_file = new PlainLogFile('fake-path', 'fake-index-path');
        $line_location = new LineLocation($log_file, 123, 1);

        $serialized = $endpoint->testOnlySerializePageToken($line_location, 'previous');

        $this->assertSame('{"logFile":"{\"class\":\"Olz\\\\\\\Apps\\\\\\\Logs\\\\\\\Utils\\\\\\\PlainLogFile\",\"path\":\"fake-path\",\"indexPath\":\"fake-index-path\"}","lineNumber":123,"comparison":1,"mode":"previous"}', $serialized);
    }

    public function testGetLogsEndpointDeserializePageToken(): void {
        $endpoint = new TestOnlyGetLogsEndpoint();
        $serialized = '{"logFile":"{\"class\":\"Olz\\\\\\\Apps\\\\\\\Logs\\\\\\\Utils\\\\\\\GzLogFile\",\"path\":\"fake-path\",\"indexPath\":\"fake-index-path\"}","lineNumber":321,"comparison":-1,"mode":"next"}';

        $result = $endpoint->testOnlyDeserializePageToken($serialized);

        $this->assertSame('{"class":"Olz\\\Apps\\\Logs\\\Utils\\\GzLogFile","path":"fake-path","indexPath":"fake-index-path"}', $result['lineLocation']->logFile->serialize());
        $this->assertSame(321, $result['lineLocation']->lineNumber);
        $this->assertSame(-1, $result['lineLocation']->comparison);
        $this->assertSame('next', $result['mode']);
    }
}
