<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Entity\Karten\Karte;
use Olz\Karten\Endpoints\UpdateKarteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeUpdateKarteEndpointKartenRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new Karte();
            $entry->setId(123);
            $entry->setOnOff(true);
            return $entry;
        }
        if ($where === ['id' => 9999]) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

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
            'position' => 1,
            'kartennr' => 12345,
            'name' => 'Test Karte',
            'centerX' => 600000,
            'centerY' => 300000,
            'year' => 2020,
            'scale' => '1:10\'000',
            'place' => 'Testiswil',
            'zoom' => 3,
            'kind' => 'stadt',
            'previewImageId' => 'uploaded_image.jpg',
        ],
    ];

    public function testUpdateKarteEndpointIdent(): void {
        $endpoint = new UpdateKarteEndpoint();
        $this->assertSame('UpdateKarteEndpoint', $endpoint->getIdent());
    }

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
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateKarteEndpointNoSuchEntity(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeUpdateKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
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
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateKarteEndpointNoEntityAccess(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeUpdateKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
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
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateKarteEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeUpdateKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
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
            'status' => 'OK',
            'id' => 123,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $karte = $entity_manager->persisted[0];
        $this->assertSame(123, $karte->getId());
        $this->assertSame(1, $karte->getPosition());
        $this->assertSame(12345, $karte->getKartenNr());
        $this->assertSame('Test Karte', $karte->getName());
        $this->assertSame(600000, $karte->getCenterX());
        $this->assertSame(300000, $karte->getCenterY());
        $this->assertSame(2020, $karte->getYear());
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
    }
}
