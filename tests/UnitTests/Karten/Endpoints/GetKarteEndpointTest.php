<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Entity\Karten\Karte;
use Olz\Karten\Endpoints\GetKarteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetKarteEndpointKartenRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $entry = new Karte();
            $entry->setId(12);
            $entry->setPosition(12);
            $entry->setName('');
            $entry->setOnOff(true);
            return $entry;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $entry = new Karte();
            $entry->setId(123);
            $entry->setPosition(0);
            $entry->setKartenNr(0);
            $entry->setName('');
            $entry->setCenterX(null);
            $entry->setCenterY(null);
            $entry->setYear(null);
            $entry->setScale('');
            $entry->setPlace('');
            $entry->setZoom(null);
            $entry->setKind(null);
            $entry->setPreviewImageId('');
            $entry->setOnOff(false);
            return $entry;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $entry = new Karte();
            $entry->setId(1234);
            $entry->setPosition(12);
            $entry->setKartenNr(12);
            $entry->setName('Fake Karte');
            $entry->setCenterX(1200000);
            $entry->setCenterY(120000);
            $entry->setYear(1200);
            $entry->setScale('1:1\'200');
            $entry->setPlace('Fake Place');
            $entry->setZoom(12);
            $entry->setKind('ol');
            $entry->setPreviewImageId('upload.jpg');
            $entry->setOnOff(true);
            return $entry;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Karten\Endpoints\GetKarteEndpoint
 */
final class GetKarteEndpointTest extends UnitTestCase {
    public function testGetKarteEndpointIdent(): void {
        $endpoint = new GetKarteEndpoint();
        $this->assertSame('GetKarteEndpoint', $endpoint->getIdent());
    }

    public function testGetKarteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetKarteEndpoint();
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

    public function testGetKarteEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeGetKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
        $endpoint = new GetKarteEndpoint();
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
                'position' => 12,
                'kartennr' => null,
                'name' => '-',
                'centerX' => null,
                'centerY' => null,
                'year' => null,
                'scale' => null,
                'place' => null,
                'zoom' => null,
                'kind' => null,
                'previewImageId' => null,
            ],
        ], $result);
    }

    public function testGetKarteEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeGetKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
        $endpoint = new GetKarteEndpoint();
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
                'position' => 0,
                'kartennr' => 0,
                'name' => '-',
                'centerX' => null,
                'centerY' => null,
                'year' => null,
                'scale' => null,
                'place' => null,
                'zoom' => null,
                'kind' => null,
                'previewImageId' => null,
            ],
        ], $result);
    }

    public function testGetKarteEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $karten_repo = new FakeGetKarteEndpointKartenRepository();
        $entity_manager->repositories[Karte::class] = $karten_repo;
        $endpoint = new GetKarteEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/downloads/');
        mkdir(__DIR__.'/../../tmp/files/downloads/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/downloads/1234/file1.pdf', '');

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
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'position' => 12,
                'kartennr' => 12,
                'name' => 'Fake Karte',
                'centerX' => 1200000,
                'centerY' => 120000,
                'year' => 1200,
                'scale' => '1:1\'200',
                'place' => 'Fake Place',
                'zoom' => 12,
                'kind' => 'ol',
                'previewImageId' => 'upload.jpg',
            ],
        ], $result);
    }
}
