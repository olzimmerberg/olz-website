<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\GetTerminLocationEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminLocationEndpoint
 */
final class GetTerminLocationEndpointTest extends UnitTestCase {
    public function testGetTerminLocationEndpointIdent(): void {
        $endpoint = new GetTerminLocationEndpoint();
        $this->assertSame('GetTerminLocationEndpoint', $endpoint->getIdent());
    }

    public function testGetTerminLocationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminLocationEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetTerminLocationEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminLocationEndpoint();
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
                'name' => 'Fake title',
                'details' => '',
                'latitude' => 0,
                'longitude' => 0,
                'imageIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminLocationEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminLocationEndpoint();
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
                'name' => 'Cannot be empty',
                'details' => '',
                'latitude' => 0,
                'longitude' => 0,
                'imageIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminLocationEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminLocationEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termin_locations/');
        mkdir(__DIR__.'/../../tmp/files/termin_locations/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/termin_locations/1234/file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termin_locations/1234/file2.pdf', '');

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
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'name' => 'Fake title',
                'details' => 'Fake content',
                'latitude' => 47.2790953,
                'longitude' => 8.5591936,
                'imageIds' => ['image__________________1.jpg', 'image__________________2.png'],
            ],
        ], $result);
    }
}
