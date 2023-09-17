<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Termine\Endpoints\UpdateTerminLocationEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeUpdateTerminLocationEndpointTerminLocationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new TerminLocation();
            $entry->setId(123);
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
 * @covers \Olz\Termine\Endpoints\UpdateTerminLocationEndpoint
 */
final class UpdateTerminLocationEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'name' => 'Test location',
            'details' => 'some location info',
            'latitude' => 47.2790953,
            'longitude' => 8.5591936,
            'imageIds' => ['uploaded_image.jpg', 'inexistent.png'],
        ],
    ];

    public function testUpdateTerminLocationEndpointIdent(): void {
        $endpoint = new UpdateTerminLocationEndpoint();
        $this->assertSame('UpdateTerminLocationEndpoint', $endpoint->getIdent());
    }

    public function testUpdateTerminLocationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new UpdateTerminLocationEndpoint();
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

    public function testUpdateTerminLocationEndpointNoSuchEntity(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeUpdateTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminLocationEndpoint();
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

    public function testUpdateTerminLocationEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeUpdateTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminLocationEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termin_locations/');

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
        $termin_location = $entity_manager->persisted[0];
        $this->assertSame(123, $termin_location->getId());
        $this->assertSame('Test location', $termin_location->getName());
        $this->assertSame('some location info', $termin_location->getDetails());
        $this->assertSame(47.2790953, $termin_location->getLatitude());
        $this->assertSame(8.5591936, $termin_location->getLongitude());
        $this->assertSame(
            ['uploaded_image.jpg', 'inexistent.png'],
            $termin_location->getImageIds(),
        );
        $this->assertSame([
            [$termin_location, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.png'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/termin_locations/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
