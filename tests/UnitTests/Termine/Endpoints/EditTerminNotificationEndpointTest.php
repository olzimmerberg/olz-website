<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\EditTerminNotificationEndpoint;
use Olz\Tests\Fake\Entity\Termine\FakeTermin;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\EditTerminNotificationEndpoint
 */
final class EditTerminNotificationEndpointTest extends UnitTestCase {
    public function testEditTerminNotificationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new EditTerminNotificationEndpoint();
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

    public function testEditTerminNotificationEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $endpoint = new EditTerminNotificationEndpoint();
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
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditTerminNotificationEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            // TerminNotification is not an OLZ entity, it is attached to the Termin entity:
            [FakeTermin::minimal(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'terminId' => 12,
                'firesEarlierSeconds' => 0,
                'title' => 'Fake title!',
                'content' => '',
                'recipientUserId' => null,
                'recipientRoleId' => null,
                'recipientTerminOwnerUser' => false,
                'recipientTerminOwnerRole' => false,
                'recipientTerminOrganizer' => false,
                'recipientTerminVolunteers' => false,
                'recipientTerminParticipants' => false,
            ],
        ], $result);
    }

    public function testEditTerminNotificationEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            // TerminNotification is not an OLZ entity, it is attached to the Termin entity:
            [FakeTermin::empty(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'terminId' => 123,
                'firesEarlierSeconds' => 0,
                'title' => 'Cannot be empty',
                'content' => '',
                'recipientUserId' => 123,
                'recipientRoleId' => 123,
                'recipientTerminOwnerUser' => false,
                'recipientTerminOwnerRole' => false,
                'recipientTerminOrganizer' => false,
                'recipientTerminVolunteers' => false,
                'recipientTerminParticipants' => false,
            ],
        ], $result);
    }

    public function testEditTerminNotificationEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            // TerminNotification is not an OLZ entity, it is attached to the Termin entity:
            [FakeTermin::maximal(), 'default', 'default', 'role', null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'terminId' => 1234,
                'firesEarlierSeconds' => 86400,
                'title' => 'Fake title!',
                'content' => 'Fake content!',
                'recipientUserId' => 1234,
                'recipientRoleId' => 1234,
                'recipientTerminOwnerUser' => true,
                'recipientTerminOwnerRole' => true,
                'recipientTerminOrganizer' => true,
                'recipientTerminVolunteers' => true,
                'recipientTerminParticipants' => true,
            ],
        ], $result);
    }
}
