<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Termine\Endpoints\DeleteTerminLocationEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeDeleteTerminLocationEndpointTerminLocationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new TerminLocation();
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
 * @covers \Olz\Termine\Endpoints\DeleteTerminLocationEndpoint
 */
final class DeleteTerminLocationEndpointTest extends UnitTestCase {
    public function testDeleteTerminLocationEndpointIdent(): void {
        $endpoint = new DeleteTerminLocationEndpoint();
        $this->assertSame('DeleteTerminLocationEndpoint', $endpoint->getIdent());
    }

    public function testDeleteTerminLocationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new DeleteTerminLocationEndpoint();
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

    public function testDeleteTerminLocationEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeDeleteTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteTerminLocationEndpoint();
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
        $termin_location = $entity_manager->persisted[0];
        $this->assertSame(123, $termin_location->getId());
        $this->assertSame(0, $termin_location->getOnOff());
    }

    public function testDeleteTerminLocationEndpointInexistent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeDeleteTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteTerminLocationEndpoint();
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
