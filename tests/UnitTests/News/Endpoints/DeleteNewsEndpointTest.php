<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\News\Endpoints\DeleteNewsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeDeleteNewsEndpointNewsRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new NewsEntry();
            $entry->setId(123);
            return $entry;
        }
        if ($where === ['id' => 9999]) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\DeleteNewsEndpoint
 */
final class DeleteNewsEndpointTest extends UnitTestCase {
    public function testDeleteNewsEndpointIdent(): void {
        $endpoint = new DeleteNewsEndpoint();
        $this->assertSame('DeleteNewsEndpoint', $endpoint->getIdent());
    }

    public function testDeleteNewsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteNewsEndpoint();
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

    public function testDeleteNewsEndpointNoEntityAccess(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeDeleteNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = false;
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteNewsEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
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

    public function testDeleteNewsEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeDeleteNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = true;
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteNewsEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(1, count($entity_manager->flushed_removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $news_entry = $entity_manager->removed[0];
        $this->assertSame(123, $news_entry->getId());
    }

    public function testDeleteNewsEndpointInexistent(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeDeleteNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = true;
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteNewsEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 9999,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([
            'status' => 'ERROR',
        ], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }
}
