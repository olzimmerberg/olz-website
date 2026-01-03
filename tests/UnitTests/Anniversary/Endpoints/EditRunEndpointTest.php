<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Anniversary\Endpoints;

use Olz\Anniversary\Endpoints\EditRunEndpoint;
use Olz\Tests\Fake\Entity\Anniversary\FakeRunRecord;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Anniversary\Endpoints\EditRunEndpoint
 */
final class EditRunEndpointTest extends UnitTestCase {
    public function testEditRunEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new EditRunEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditRunEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new EditRunEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditRunEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditRunEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeRunRecord::empty(), null, null, null, null, 'anniversary'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditRunEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditRunEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeRunRecord::minimal(), null, null, null, null, 'anniversary'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertEquals([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'userId' => null,
                'runAt' => '2020-08-15 16:27:00',
                'distanceMeters' => 0,
                'elevationMeters' => 0,
                'sportType' => null,
                'source' => null,
            ],
        ], $result);
    }

    public function testEditRunEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditRunEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeRunRecord::empty(), null, null, null, null, 'anniversary'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertEquals([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'userId' => 123,
                'runAt' => '-0001-11-30 00:00:00',
                'distanceMeters' => 0,
                'elevationMeters' => 0,
                'sportType' => '',
                'source' => null,
            ],
        ], $result);
    }

    public function testEditRunEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditRunEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeRunRecord::maximal(), 'default', 'default', 'role', null, 'anniversary'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertEquals([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'userId' => 1234,
                'runAt' => '2020-08-15 16:27:00',
                'distanceMeters' => 3000,
                'elevationMeters' => 200,
                'sportType' => 'Maximal Run',
                'source' => 'shady_source',
            ],
        ], $result);
    }
}
