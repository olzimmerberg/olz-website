<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Karten\Endpoints\UpdateKarteEndpoint;
use Olz\Tests\Fake\Entity\Karten\FakeKarte;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Karten\Endpoints\UpdateKarteEndpoint
 */
final class UpdateKarteEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'kartennr' => 12345,
            'name' => 'Test Karte',
            'latitude' => 47.3,
            'longitude' => 8.7,
            'year' => 2020,
            'scale' => '1:10\'000',
            'place' => 'Testiswil',
            'zoom' => 3,
            'kind' => 'stadt',
            'previewImageId' => 'uploaded_image.jpg',
        ],
    ];

    public function testUpdateKarteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateKarteEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::VALID_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateKarteEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateKarteEndpoint();
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
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls
            );
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateKarteEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateKarteEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::VALID_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame([
                [FakeKarte::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'karten'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateKarteEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateKarteEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/karten/');

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => 123,
        ], $result);
        $this->assertSame([
            [FakeKarte::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'karten'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $karte = $entity_manager->persisted[0];
        $this->assertSame(123, $karte->getId());
        $this->assertSame(12345, $karte->getKartenNr());
        $this->assertSame('Test Karte', $karte->getName());
        $this->assertSame(47.3, $karte->getLatitude());
        $this->assertSame(8.7, $karte->getLongitude());
        $this->assertSame('2020', $karte->getYear());
        $this->assertSame('1:10\'000', $karte->getScale());
        $this->assertSame('Testiswil', $karte->getPlace());
        $this->assertSame(3, $karte->getZoom());
        $this->assertSame('stadt', $karte->getKind());
        $this->assertSame('uploaded_image.jpg', $karte->getPreviewImageId());

        $this->assertSame([
            [$karte, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/karten/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/karten/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
