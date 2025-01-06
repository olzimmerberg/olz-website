<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Karten\Endpoints\CreateKarteEndpoint;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Karten\Endpoints\CreateKarteEndpoint
 */
final class CreateKarteEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'kartennr' => 12345,
            'name' => 'Test Karte',
            'latitude' => 47.2,
            'longitude' => 8.6,
            'year' => 2020,
            'scale' => '1:10\'000',
            'place' => 'Testiswil',
            'zoom' => 3,
            'kind' => 'stadt',
            'previewImageId' => 'uploaded_image.jpg',
        ],
    ];

    public function testCreateKarteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['karten' => false];
        $endpoint = new CreateKarteEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::VALID_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testCreateKarteEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['karten' => true];
        $endpoint = new CreateKarteEndpoint();
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
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $karte = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $karte->getId());
        $this->assertSame(12345, $karte->getKartenNr());
        $this->assertSame('Test Karte', $karte->getName());
        $this->assertSame(47.2, $karte->getLatitude());
        $this->assertSame(8.6, $karte->getLongitude());
        $this->assertSame('2020', $karte->getYear());
        $this->assertSame('1:10\'000', $karte->getScale());
        $this->assertSame('Testiswil', $karte->getPlace());
        $this->assertSame(3, $karte->getZoom());
        $this->assertSame('stadt', $karte->getKind());
        $this->assertSame('uploaded_image.jpg', $karte->getPreviewImageId());

        $this->assertSame([
            [$karte, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

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
