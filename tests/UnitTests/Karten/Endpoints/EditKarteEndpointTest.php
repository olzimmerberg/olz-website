<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Karten\Endpoints\EditKarteEndpoint;
use Olz\Tests\Fake\Entity\Karten\FakeKarte;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Karten\Endpoints\EditKarteEndpoint
 */
final class EditKarteEndpointTest extends UnitTestCase {
    public function testEditKarteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new EditKarteEndpoint();
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

    public function testEditKarteEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new EditKarteEndpoint();
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
        }
    }

    public function testEditKarteEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditKarteEndpoint();
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
            $this->assertSame([
                [FakeKarte::empty(), null, null, null, null, 'karten'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditKarteEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditKarteEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Upload ID \"\" is invalid.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            [FakeKarte::minimal(), null, null, null, null, 'karten'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'kartennr' => null,
                'name' => '-',
                'latitude' => null,
                'longitude' => null,
                'year' => null,
                'scale' => null,
                'place' => null,
                'zoom' => null,
                'kind' => null,
                'previewImageId' => null,
            ],
        ], $result);
    }

    public function testEditKarteEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditKarteEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Upload ID \"\" is invalid.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            [FakeKarte::empty(), null, null, null, null, 'karten'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'kartennr' => 0,
                'name' => '-',
                'latitude' => null,
                'longitude' => null,
                'year' => null,
                'scale' => null,
                'place' => null,
                'zoom' => null,
                'kind' => null,
                'previewImageId' => null,
            ],
        ], $result);
    }

    public function testEditKarteEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditKarteEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/karten/');
        mkdir(__DIR__.'/../../tmp/img/karten/1234/');
        mkdir(__DIR__.'/../../tmp/img/karten/1234/img');
        file_put_contents(__DIR__.'/../../tmp/img/karten/1234/img/image__________________1.jpg', '');

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            [FakeKarte::maximal(), 'default', 'default', 'role', null, 'karten'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'kartennr' => 12,
                'name' => 'Fake Karte',
                'latitude' => 47.2,
                'longitude' => 8.6,
                'year' => 1200,
                'scale' => '1:1\'200',
                'place' => 'Fake Place',
                'zoom' => 12,
                'kind' => 'ol',
                'previewImageId' => 'image__________________1.jpg',
            ],
        ], $result);
    }
}
