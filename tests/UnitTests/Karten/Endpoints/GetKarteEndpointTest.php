<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Karten\Endpoints\GetKarteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Karten\Endpoints\GetKarteEndpoint
 */
final class GetKarteEndpointTest extends UnitTestCase {
    public function testGetKarteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetKarteEndpoint();
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

    public function testGetKarteEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetKarteEndpoint();
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
                'kartennr' => null,
                'name' => '-',
                'latitude' => null,
                'longitude' => null,
                'year' => null,
                'scale' => null,
                'place' => null,
                'zoom' => null,
                'kind' => null,
                'previewImageId' => null,
            ],
        ], $result);
    }

    public function testGetKarteEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetKarteEndpoint();
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
                'kartennr' => 0,
                'name' => '-',
                'latitude' => null,
                'longitude' => null,
                'year' => null,
                'scale' => null,
                'place' => null,
                'zoom' => null,
                'kind' => null,
                'previewImageId' => null,
            ],
        ], $result);
    }

    public function testGetKarteEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetKarteEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/downloads/');
        mkdir(__DIR__.'/../../tmp/files/downloads/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/downloads/1234/image__________________1.jpg', '');

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
                'kartennr' => 12,
                'name' => 'Fake Karte',
                'latitude' => 47.2,
                'longitude' => 8.6,
                'year' => 1200,
                'scale' => '1:1\'200',
                'place' => 'Fake Place',
                'zoom' => 12,
                'kind' => 'ol',
                'previewImageId' => 'image__________________1.jpg',
            ],
        ], $result);
    }
}
