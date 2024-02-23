<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Entity\Karten\Karte;
use Olz\Karten\Endpoints\DeleteKarteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeDeleteKarteEndpointKartenRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new Karte();
            $entry->setId(123);
            $entry->setOnOff(true);
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
 * @covers \Olz\Karten\Endpoints\DeleteKarteEndpoint
 */
final class DeleteKarteEndpointTest extends UnitTestCase {
    public function testDeleteKarteEndpointIdent(): void {
        $endpoint = new DeleteKarteEndpoint();
        $this->assertSame('DeleteKarteEndpoint', $endpoint->getIdent());
    }

    public function testDeleteKarteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new DeleteKarteEndpoint();
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

    public function testDeleteKarteEndpointNoEntityAccess(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeDeleteKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new DeleteKarteEndpoint();
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

    public function testDeleteKarteEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeDeleteKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteKarteEndpoint();
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

    public function testDeleteKarteEndpointInexistent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeDeleteKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteKarteEndpoint();
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
            $this->assertSame(0, count($entity_manager->removed));
            $this->assertSame(0, count($entity_manager->flushed_removed));
        }
    }
}
