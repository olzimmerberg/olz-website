<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\GetTerminNotificationEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminNotificationEndpoint
 */
final class GetTerminNotificationEndpointTest extends UnitTestCase {
    public function testGetTerminNotificationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminNotificationEndpoint();
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

    public function testGetTerminNotificationEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
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

    public function testGetTerminNotificationEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
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

    public function testGetTerminNotificationEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminNotificationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
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
