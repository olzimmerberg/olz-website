<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Startseite\Endpoints\EditWeeklyPictureEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Startseite\Endpoints\EditWeeklyPictureEndpoint
 */
final class EditWeeklyPictureEndpointTest extends UnitTestCase {
    public function testEditWeeklyPictureEndpointIdent(): void {
        $endpoint = new EditWeeklyPictureEndpoint();
        $this->assertSame('EditWeeklyPictureEndpoint', $endpoint->getIdent());
    }

    public function testEditWeeklyPictureEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new EditWeeklyPictureEndpoint();
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

    public function testEditWeeklyPictureEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new EditWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditWeeklyPictureEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditWeeklyPictureEndpoint();
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

    public function testEditWeeklyPictureEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Upload ID \"\" is invalid.",
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

    public function testEditWeeklyPictureEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Upload ID \"\" is invalid.",
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

    public function testEditWeeklyPictureEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditWeeklyPictureEndpoint();
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
