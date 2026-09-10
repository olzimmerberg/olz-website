<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\UpdateTerminNotificationEndpoint;
use Olz\Tests\Fake\Entity\Termine\FakeTermin;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\UpdateTerminNotificationEndpoint
 */
final class UpdateTerminNotificationEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'terminId' => 1234,
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

    public function testUpdateTerminNotificationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new UpdateTerminNotificationEndpoint();
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

    public function testUpdateTerminNotificationEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminNotificationEndpoint();
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

    public function testUpdateTerminNotificationEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminNotificationEndpoint();
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
            // TerminNotification is not an OLZ entity, it is attached to the Termin entity:
            [FakeTermin::empty(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(123, $entity->getId());
        $this->assertSame(1234, $entity->getTermin()?->getId());
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
        // TerminNotification is not an OLZ entity, it is attached to the Termin entity:
        $this->assertSame([], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);
    }
}
