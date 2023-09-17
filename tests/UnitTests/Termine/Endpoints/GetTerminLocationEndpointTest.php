<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Termine\Endpoints\GetTerminLocationEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetTerminLocationEndpointTerminLocationRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $termin_location = new TerminLocation();
            $termin_location->setId(12);
            $termin_location->setName("Fake title");
            $termin_location->setDetails("");
            $termin_location->setLatitude(0);
            $termin_location->setLongitude(0);
            $termin_location->setOnOff(true);
            return $termin_location;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $termin_location = new TerminLocation();
            $termin_location->setId(123);
            $termin_location->setName("Cannot be empty");
            $termin_location->setDetails("");
            $termin_location->setLatitude(0);
            $termin_location->setLongitude(0);
            $termin_location->setImageIds([]);
            $termin_location->setOnOff(false);
            return $termin_location;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $termin_location = new TerminLocation();
            $termin_location->setId(1234);
            $termin_location->setName("Fake title");
            $termin_location->setDetails("Fake content");
            $termin_location->setLatitude(47.2790953);
            $termin_location->setLongitude(8.5591936);
            $termin_location->setImageIds(['img1.jpg', 'img2.png']);
            $termin_location->setOnOff(true);
            return $termin_location;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminLocationEndpoint
 */
final class GetTerminLocationEndpointTest extends UnitTestCase {
    public function testGetTerminLocationEndpointIdent(): void {
        $endpoint = new GetTerminLocationEndpoint();
        $this->assertSame('GetTerminLocationEndpoint', $endpoint->getIdent());
    }

    public function testGetTerminLocationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminLocationEndpoint();
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

    public function testGetTerminLocationEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeGetTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        $endpoint = new GetTerminLocationEndpoint();
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
                'name' => 'Fake title',
                'details' => '',
                'latitude' => 0,
                'longitude' => 0,
                'imageIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminLocationEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeGetTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        $endpoint = new GetTerminLocationEndpoint();
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
                'name' => 'Cannot be empty',
                'details' => '',
                'latitude' => 0,
                'longitude' => 0,
                'imageIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminLocationEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeGetTerminLocationEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        $endpoint = new GetTerminLocationEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termin_locations/');
        mkdir(__DIR__.'/../../tmp/files/termin_locations/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/termin_locations/1234/file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termin_locations/1234/file2.pdf', '');

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
                'name' => 'Fake title',
                'details' => 'Fake content',
                'latitude' => 47.2790953,
                'longitude' => 8.5591936,
                'imageIds' => ['img1.jpg', 'img2.png'],
            ],
        ], $result);
    }
}
