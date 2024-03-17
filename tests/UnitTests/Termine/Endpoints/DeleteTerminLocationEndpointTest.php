<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\DeleteTerminLocationEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

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
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $termin_location = $entity_manager->persisted[0];
        $this->assertSame(123, $termin_location->getId());
        $this->assertSame(0, $termin_location->getOnOff());
    }

    public function testDeleteTerminLocationEndpointInexistent(): void {
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
            $entity_manager = WithUtilsCache::get('entityManager');
            $this->assertSame(0, count($entity_manager->removed));
            $this->assertSame(0, count($entity_manager->flushed_removed));
        }
    }
}
