<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\GetNewsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\GetNewsEndpoint
 */
final class GetNewsEndpointTest extends UnitTestCase {
    public function testGetNewsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetNewsEndpoint();
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

    public function testGetNewsEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetNewsEndpoint();
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
                'format' => 'aktuell',
                'authorUserId' => null,
                'authorRoleId' => null,
                'authorName' => null,
                'authorEmail' => null,
                'publishAt' => '2020-03-13 18:00:00',
                'title' => 'Fake title',
                'teaser' => '',
                'content' => '',
                'externalUrl' => null,
                'tags' => [],
                'terminId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetNewsEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetNewsEndpoint();
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
                'format' => 'aktuell',
                'authorUserId' => null,
                'authorRoleId' => null,
                'authorName' => null,
                'authorEmail' => null,
                'publishAt' => '0000-01-01 00:00:00',
                'title' => 'Cannot be empty',
                'teaser' => '',
                'content' => '',
                'externalUrl' => null,
                'tags' => [],
                'terminId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetNewsEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetNewsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/news/');
        mkdir(__DIR__.'/../../tmp/img/news/1234/');
        mkdir(__DIR__.'/../../tmp/img/news/1234/img/');
        file_put_contents(__DIR__.'/../../tmp/img/news/1234/img/image__________________1.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/img/news/1234/img/image__________________2.png', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/news/');
        mkdir(__DIR__.'/../../tmp/files/news/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/news/1234/file___________________1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/news/1234/file___________________2.txt', '');

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
                'format' => 'aktuell',
                'authorUserId' => 2,
                'authorRoleId' => 2,
                'authorName' => 'Manuel',
                'authorEmail' => 'manual-author@staging.olzimmerberg.ch',
                'publishAt' => '2020-03-13 18:00:00',
                'title' => 'Fake title',
                'teaser' => 'Fake teaser',
                'content' => 'Fake content',
                'externalUrl' => null,
                'tags' => ['test', 'unit'],
                'terminId' => null,
                'imageIds' => ['image__________________1.jpg', 'image__________________2.png'],
                'fileIds' => ['file___________________1.pdf', 'file___________________2.txt'],
            ],
        ], $result);
    }
}
