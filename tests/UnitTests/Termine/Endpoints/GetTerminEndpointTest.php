<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\GetTerminEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminEndpoint
 */
final class GetTerminEndpointTest extends UnitTestCase {
    public function testGetTerminEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetTerminEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'fromTemplateId' => null,
                'startDate' => '2020-03-13',
                'startTime' => null,
                'endDate' => null,
                'endTime' => null,
                'title' => 'Fake title',
                'text' => '',
                'organizerUserId' => null,
                'deadline' => null,
                'shouldPromote' => false,
                'newsletter' => false,
                'solvId' => null,
                'go2olId' => null,
                'types' => [],
                'locationId' => null,
                'coordinateX' => null,
                'coordinateY' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'fromTemplateId' => null,
                'startDate' => '0000-01-01',
                'startTime' => '00:00:00',
                'endDate' => '0000-01-01',
                'endTime' => '00:00:00',
                'title' => 'Cannot be empty',
                'text' => '',
                'organizerUserId' => null,
                'deadline' => '0000-01-01 00:00:00',
                'shouldPromote' => false,
                'newsletter' => false,
                'solvId' => null,
                'go2olId' => null,
                'types' => [],
                'locationId' => null,
                'coordinateX' => 0,
                'coordinateY' => 0,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termine/');
        mkdir(__DIR__.'/../../tmp/files/termine/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/termine/1234/file___________________1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termine/1234/file___________________2.pdf', '');

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'fromTemplateId' => 12341,
                'startDate' => '2020-03-13',
                'startTime' => '19:30:00',
                'endDate' => '2020-03-16',
                'endTime' => '12:00:00',
                'title' => 'Fake title',
                'text' => 'Fake content',
                'organizerUserId' => 12342,
                'deadline' => '2020-03-13 18:00:00',
                'shouldPromote' => true,
                'newsletter' => true,
                'solvId' => 1234,
                'go2olId' => 'deprecated',
                'types' => ['training', 'weekends'],
                'locationId' => 12345,
                'coordinateX' => 684835,
                'coordinateY' => 237021,
                'imageIds' => ['image__________________1.jpg', 'image__________________2.png'],
                'fileIds' => ['file___________________1.pdf', 'file___________________2.pdf'],
            ],
        ], $result);
    }
}
