<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\CreateTerminNotificationEndpoint;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\CreateTerminNotificationEndpoint
 */
final class CreateTerminNotificationEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
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

    public function testCreateTerminNotificationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new CreateTerminNotificationEndpoint();
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

    public function testCreateTerminNotificationEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $endpoint = new CreateTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
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
        $this->assertSame([], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }
}
