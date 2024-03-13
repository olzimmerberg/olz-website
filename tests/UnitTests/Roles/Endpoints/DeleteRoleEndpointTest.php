<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Roles\Endpoints\DeleteRoleEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeDeleteRoleEndpointRoleRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new Role();
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
 * @covers \Olz\Roles\Endpoints\DeleteRoleEndpoint
 */
final class DeleteRoleEndpointTest extends UnitTestCase {
    public function testDeleteRoleEndpointIdent(): void {
        $endpoint = new DeleteRoleEndpoint();
        $this->assertSame('DeleteRoleEndpoint', $endpoint->getIdent());
    }

    public function testDeleteRoleEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        $endpoint = new DeleteRoleEndpoint();
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

    public function testDeleteRoleEndpointNoEntityAccess(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeDeleteRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new DeleteRoleEndpoint();
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

    public function testDeleteRoleEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeDeleteRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteRoleEndpoint();
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

    public function testDeleteRoleEndpointInexistent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeDeleteRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteRoleEndpoint();
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
