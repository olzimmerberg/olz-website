<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\CreateTerminLocationEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\CreateTerminLocationEndpoint
 */
final class CreateTerminLocationEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
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

    public function testCreateTerminLocationEndpointIdent(): void {
        $endpoint = new CreateTerminLocationEndpoint();
        $this->assertSame('CreateTerminLocationEndpoint', $endpoint->getIdent());
    }

    public function testCreateTerminLocationEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new CreateTerminLocationEndpoint();
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

    public function testCreateTerminLocationEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $endpoint = new CreateTerminLocationEndpoint();
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
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $termin_location = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $termin_location->getId());
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
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = Fake\FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.png'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/termin_locations/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
