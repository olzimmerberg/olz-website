<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\News\Endpoints\EditNewsEndpoint;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

class FakeEditNewsEndpointNewsRepository {
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
 * @covers \Olz\News\Endpoints\EditNewsEndpoint
 */
final class EditNewsEndpointTest extends UnitTestCase {
    public function testEditNewsEndpointIdent(): void {
        $endpoint = new EditNewsEndpoint();
        $this->assertSame('EditNewsEndpoint', $endpoint->getIdent());
    }

    public function testEditNewsEndpointNoAccess(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['news' => false];
        $logger = FakeLogger::create();
        $endpoint = new EditNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditNewsEndpointMaximal(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['news' => true];
        $entity_manager = new FakeEntityManager();
        $news_repo = new FakeEditNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new EditNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/');
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
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'format' => 'aktuell',
                'author' => null,
                'authorUserId' => null,
                'authorRoleId' => null,
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
