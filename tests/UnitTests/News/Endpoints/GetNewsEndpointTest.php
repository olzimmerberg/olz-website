<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\News\Endpoints\GetNewsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetNewsEndpointNewsRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 12]) {
            $entry = new NewsEntry();
            $entry->setId(12);
            $entry->setFormat('aktuell');
            $entry->setTitle("Fake title");
            $entry->setTeaser("");
            $entry->setContent("");
            return $entry;
        }
        if ($where === ['id' => 123]) {
            $entry = new NewsEntry();
            $entry->setId(123);
            $entry->setFormat('aktuell');
            $entry->setTitle("Fake title");
            $entry->setTeaser("Fake teaser");
            $entry->setContent("Fake content");
            $entry->setTags(' test unit ');
            $entry->setImageIds(['pictureA.jpg', 'pictureB.jpg']);
            return $entry;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\GetNewsEndpoint
 */
final class GetNewsEndpointTest extends UnitTestCase {
    public function testGetNewsEndpointIdent(): void {
        $endpoint = new GetNewsEndpoint();
        $this->assertSame('GetNewsEndpoint', $endpoint->getIdent());
    }

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
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeGetNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
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
                'onOff' => false,
            ],
            'data' => [
                'format' => 'aktuell',
                'authorUserId' => null,
                'authorRoleId' => null,
                'authorName' => null,
                'authorEmail' => null,
                'title' => 'Fake title',
                'teaser' => '',
                'content' => '',
                'externalUrl' => null,
                'tags' => [],
                'terminId' => null,
                'imageIds' => null,
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetNewsEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeGetNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $endpoint = new GetNewsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/news/');
        mkdir(__DIR__.'/../../tmp/img/news/123/');
        mkdir(__DIR__.'/../../tmp/img/news/123/img/');
        file_put_contents(__DIR__.'/../../tmp/img/news/123/img/pictureA.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/img/news/123/img/pictureB.jpg', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/news/');
        mkdir(__DIR__.'/../../tmp/files/news/123/');
        file_put_contents(__DIR__.'/../../tmp/files/news/123/file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/news/123/file2.pdf', '');

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
                'title' => 'Fake title',
                'teaser' => 'Fake teaser',
                'content' => 'Fake content',
                'externalUrl' => null,
                'tags' => ['test', 'unit'],
                'terminId' => null,
                'imageIds' => ['pictureA.jpg', 'pictureB.jpg'],
                'fileIds' => ['file1.pdf', 'file2.pdf'],
            ],
        ], $result);
    }
}
