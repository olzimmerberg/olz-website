<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Entity\Service\Download;
use Olz\Service\Endpoints\DeleteDownloadEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeDeleteDownloadEndpointDownloadRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new Download();
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
 * @covers \Olz\Service\Endpoints\DeleteDownloadEndpoint
 */
final class DeleteDownloadEndpointTest extends UnitTestCase {
    public function testDeleteDownloadEndpointIdent(): void {
        $endpoint = new DeleteDownloadEndpoint();
        $this->assertSame('DeleteDownloadEndpoint', $endpoint->getIdent());
    }

    public function testDeleteDownloadEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new DeleteDownloadEndpoint();
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

    public function testDeleteDownloadEndpointNoEntityAccess(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeDeleteDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new DeleteDownloadEndpoint();
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

    public function testDeleteDownloadEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeDeleteDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteDownloadEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $download = $entity_manager->persisted[0];
        $this->assertSame(123, $download->getId());
        $this->assertSame(0, $download->getOnOff());
    }

    public function testDeleteDownloadEndpointInexistent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeDeleteDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteDownloadEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 9999,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'ERROR',
        ], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }
}
