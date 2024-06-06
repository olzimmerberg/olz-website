<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Startseite\Endpoints\GetWeeklyPictureEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Startseite\Endpoints\GetWeeklyPictureEndpoint
 */
final class GetWeeklyPictureEndpointTest extends UnitTestCase {
    public function testGetWeeklyPictureEndpointIdent(): void {
        $endpoint = new GetWeeklyPictureEndpoint();
        $this->assertSame('GetWeeklyPictureEndpoint', $endpoint->getIdent());
    }

    public function testGetWeeklyPictureEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetWeeklyPictureEndpoint();
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

    public function testGetWeeklyPictureEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetWeeklyPictureEndpoint();
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
                'text' => '',
                'imageId' => '-',
                'publishedDate' => null,
            ],
        ], $result);
    }

    public function testGetWeeklyPictureEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetWeeklyPictureEndpoint();
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
                'text' => '',
                'imageId' => '-',
                'publishedDate' => '0000-01-01',
            ],
        ], $result);
    }

    public function testGetWeeklyPictureEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/weekly_picture/');
        mkdir(__DIR__.'/../../tmp/img/weekly_picture/1234/');
        mkdir(__DIR__.'/../../tmp/img/weekly_picture/1234/img/');
        file_put_contents(__DIR__.'/../../tmp/img/weekly_picture/1234/img/image__________________1.jpg', '');

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
                'text' => 'Fake text',
                'imageId' => 'image__________________1.jpg',
                'publishedDate' => '2020-03-13',
            ],
        ], $result);
    }
}
