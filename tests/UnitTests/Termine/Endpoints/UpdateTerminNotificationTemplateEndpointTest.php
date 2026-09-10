<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\UpdateTerminNotificationTemplateEndpoint;
use Olz\Tests\Fake\Entity\Termine\FakeTerminTemplate;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\UpdateTerminNotificationTemplateEndpoint
 */
final class UpdateTerminNotificationTemplateEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'terminTemplateId' => 1234,
            'firesEarlierSeconds' => 3600,
            'title' => 'Test notification',
            'content' => 'some notification info',
            'recipientUserId' => 1234,
            'recipientRoleId' => null,
            'recipientTerminOwnerUser' => true,
            'recipientTerminOwnerRole' => false,
            'recipientTerminOrganizer' => true,
            'recipientTerminVolunteers' => false,
            'recipientTerminParticipants' => true,
        ],
    ];

    public function testUpdateTerminNotificationTemplateEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new UpdateTerminNotificationTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::VALID_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403 Kein Zugriff!",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateTerminNotificationTemplateEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminNotificationTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...self::VALID_INPUT,
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
        }
    }

    public function testUpdateTerminNotificationTemplateEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminNotificationTemplateEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => 123,
        ], $result);

        $this->assertSame([
            // TerminNotificationTemplate is not an OLZ entity, it is attached to the TerminTemplate entity:
            [FakeTerminTemplate::empty(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(123, $entity->getId());
        $this->assertSame(1234, $entity->getTerminTemplate()?->getId());
        $this->assertSame(3600, $entity->getFiresEarlierSeconds());
        $this->assertSame('Test notification', $entity->getTitle());
        $this->assertSame('some notification info', $entity->getContent());
        $this->assertSame(1234, $entity->getRecipientUser()?->getId());
        $this->assertNull($entity->getRecipientRole()?->getId());
        $this->assertTrue($entity->getRecipientTerminOwnerUser());
        $this->assertFalse($entity->getRecipientTerminOwnerRole());
        $this->assertTrue($entity->getRecipientTerminOrganizer());
        $this->assertFalse($entity->getRecipientTerminVolunteers());
        $this->assertTrue($entity->getRecipientTerminParticipants());
        // TerminNotificationTemplate is not an OLZ entity, it is attached to the Termin entity:
        $this->assertSame([], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);
    }
}
