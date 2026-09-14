<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\DeleteTerminNotificationTemplateEndpoint;
use Olz\Tests\Fake\Entity\Termine\FakeTerminTemplate;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\DeleteTerminNotificationTemplateEndpoint
 */
final class DeleteTerminNotificationTemplateEndpointTest extends UnitTestCase {
    public function testDeleteTerminNotificationTemplateEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new DeleteTerminNotificationTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403 Kein Zugriff!",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testDeleteTerminNotificationTemplateEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteTerminNotificationTemplateEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([], $result);

        $this->assertSame([
            // TerminNotificationTemplate is not an OLZ entity, it is attached to the TerminTemplate entity:
            [FakeTerminTemplate::empty(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->persisted);
        $this->assertCount(0, $entity_manager->flushed_persisted);
        $this->assertCount(1, $entity_manager->removed);
        $this->assertCount(1, $entity_manager->flushed_removed);
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $entity = $entity_manager->removed[0];
        $this->assertSame(123, $entity->getId());
    }

    public function testDeleteTerminNotificationTemplateEndpointInexistent(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteTerminNotificationTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404 Nicht gefunden.",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls
            );

            $this->assertSame(404, $err->getCode());
            $entity_manager = WithUtilsCache::get('entityManager');
            $this->assertCount(0, $entity_manager->removed);
            $this->assertCount(0, $entity_manager->flushed_removed);
        }
    }
}
